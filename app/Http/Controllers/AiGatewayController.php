<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class AiGatewayController extends Controller
{
    /**
     * Front -> Laravel (this proxy) -> AI service
     *
     * Request JSON:
     * {
     *   "target_url": "http://13.48.27.24",
     *   "id_project": 14,
     *   "path": "https://demo2-iwi.test/uploads/xxx.mp4",
     *   "threshold_sec": 2,
     *   "classes": ["Airplane","Boat"]
     * }
     */
    public function launch(Request $request): JsonResponse
    {
        // 1) Validate request
        $data = $request->validate([
            'target_url'    => ['required', 'url'],
            'id_project'    => ['required', 'integer', 'exists:projects,id'],
            'path'          => ['required', 'string', 'max:2048'],
            'threshold_sec' => ['required', 'integer', 'min:0', 'max:3600'],
            'classes'       => ['array'],
            'classes.*'     => ['string', 'max:200'],
        ]);

        // 2) Whitelist check
        if (!$this->isAllowedTarget($data['target_url'])) {
            return response()->json(['message' => 'Target host is not allowed'], 403);
        }

        // 3) Fetch project
        /** @var \App\Models\Project $project */
        $project = Project::query()->findOrFail($data['id_project']);

        // If we ALREADY have a task: query its result instead of launching a new one
        if (!empty($project->ai_task_id)) {
            $resultUrl = $this->buildResultUrl($data['target_url'], $project->ai_task_id);

            $resp = Http::timeout(60)
                ->acceptJson()
                ->get($resultUrl);

            if ($resp->failed()) {
                return response()->json([
                    'message' => 'Failed to query AI task result',
                    'status'  => $resp->status(),
                    'body'    => $resp->body(),
                ], 502);
            }

            $json = $resp->json();
            if (!$json && $resp->body()) {
                $maybe = json_decode($resp->body(), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $json = $maybe;
                }
            }

            // Expected shapes:
            // PROGRESS: { "state": "PROGRESS", "status": "2.27%" }
            // SUCCESS:  { "state": "SUCCESS", "result": { ... } }
            $state = $json['state'] ?? null;
            if ($state === 'PROGRESS') {
                $percent = trim((string)($json['status'] ?? ''));
                return response()->json([
                    'status'  => 'ok',
                    'state'   => 'PROGRESS',
                    'message' => $percent ? "In progress: {$percent}" : 'In progress',
                ]);
            }

            if ($state === 'SUCCESS') {
                // (Opcional) aquí podrías limpiar ai_task_id si quieres no volver a consultar
                // $project->ai_task_id = null; $project->save();

				$project->ai_task_id = null;
	    	    if (Schema::hasColumn('projects', 'ai_last_task_at')) {
    	    	    $project->ai_last_task_at = null;
				}
				$project->save();

                return response()->json([
                    'status' => 'ok',
                    'state'  => 'SUCCESS',
                    'result' => $json['result'] ?? null,
                ]);
            }

            // Any other unexpected state
            return response()->json([
                'message' => 'Unexpected AI task state',
                'raw'     => $json,
            ], 502);
        }

        // If we DON'T have a task yet: launch a new one
        $payloadAi = [
            'classes'       => $data['classes'] ?? [],
            'id_project'    => $data['id_project'],
            // Normalize path domain as you asked:
            'path'          => str_replace('demo2-iwi.test', 'uat.i-want-it.es', $data['path']),
            'threshold_sec' => $data['threshold_sec'],
        ];

        $resp = Http::timeout(120)
            ->acceptJson()
            ->asJson()
            ->post($data['target_url'], $payloadAi);

        if ($resp->failed()) {
            return response()->json([
                'message' => 'Failed to contact AI service',
                'status'  => $resp->status(),
                'body'    => $resp->body(),
            ], 502);
        }

        $json = $resp->json();
        if (!$json && $resp->body()) {
            $maybe = json_decode($resp->body(), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $json = $maybe;
            }
        }

        $taskId = $json['task_id'] ?? null;
        if (!$taskId) {
            return response()->json([
                'message' => "AI service did not return a valid 'task_id'",
                'raw'     => $resp->body(),
            ], 502);
        }

        // Save task_id in projects
        $project->ai_task_id = $taskId;
        if (Schema::hasColumn('projects', 'ai_last_task_at')) {
            $project->ai_last_task_at = now();
        }
        $project->save();

        return response()->json([
            'status'     => 'ok',
            'state'      => 'QUEUED',
            'project_id' => $project->id,
            'task_id'    => $taskId,
        ]);
    }

    /**
     * Optional: direct store if you ever need it.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'task_id'    => ['required', 'string', 'max:191'],
        ]);

        /** @var \App\Models\Project $project */
        $project = Project::query()->findOrFail($data['project_id']);
        $project->ai_task_id = $data['task_id'];
        if (Schema::hasColumn('projects', 'ai_last_task_at')) {
            $project->ai_last_task_at = now();
        }
        $project->save();

        return response()->json([
            'status'     => 'ok',
            'project_id' => $project->id,
            'task_id'    => $project->ai_task_id,
        ]);
    }

    private function isAllowedTarget(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) return false;

        $allowed = config('ai.allowed_hosts');
        if (!is_array($allowed) || empty($allowed)) {
            $env = env('AI_ALLOWED_HOSTS', '');
            $allowed = array_filter(array_map('trim', explode(',', $env)));
        }

        return in_array($host, $allowed, true);
    }

    /**
     * Build http(s)://host:5018/v1/get_result/{taskId}
     */
    private function buildResultUrl(string $baseUrl, string $taskId): string
    {
        $parts  = parse_url($baseUrl);
        $scheme = $parts['scheme'] ?? 'http';
        $host   = $parts['host']   ?? '';
        // If target_url had a path, we ignore it; spec requires fixed path
        return sprintf('%s://%s:5018/v1/get_result/%s', $scheme, $host, $taskId);
    }

	public function result(Request $request)
	{
		$data = $request->validate([
			'task_id'    => ['required','string','max:191'],
			'target_url' => ['required','url'],
		]);

		// whitelist opcional
		$host = parse_url($data['target_url'], PHP_URL_HOST);
		$allowed = config('ai.allowed_hosts') ?: array_filter(array_map('trim', explode(',', env('AI_ALLOWED_HOSTS', '13.48.27.24'))));
		if (!in_array($host, $allowed, true)) {
			return response()->json(['message' => 'Target host is not allowed'], 403);
		}

		// construir URL http(s)://host:5018/v1/get_result/{task}
		$scheme = parse_url($data['target_url'], PHP_URL_SCHEME) ?: 'http';
		$base   = $scheme.'://'.$host;
		$url    = rtrim($base, '/').':5018/v1/get_result/'.rawurlencode($data['task_id']);

		$resp = Http::timeout(30)->acceptJson()->get($url); // o ->post($url) si tu servicio exige POST
		if ($resp->failed()) {
			return response()->json([
				'message' => 'Failed to query AI result',
				'status'  => $resp->status(),
				'body'    => $resp->body(),
			], 502);
		}

		// devolver tal cual al front
		return response()->json($resp->json() ?? json_decode($resp->body(), true));
	}

	private function queryAiResult(string $targetUrl, string $taskId, int $timeout = 30): array
	{
		$host   = parse_url($targetUrl, PHP_URL_HOST);
		$scheme = parse_url($targetUrl, PHP_URL_SCHEME) ?: 'http';
		if (!$host) {
			return ['error' => 'Invalid target_url host'];
		}

		// Whitelist (opcional pero recomendable)
		$allowed = config('ai.allowed_hosts') ?: array_filter(array_map('trim', explode(',', env('AI_ALLOWED_HOSTS', '13.48.27.24'))));
		if (!in_array($host, $allowed, true)) {
			return ['error' => 'Target host is not allowed', 'code' => 403];
		}

		$base = $scheme.'://'.$host;
		$url  = rtrim($base, '/').':5018/v1/get_result/'.rawurlencode($taskId);

		$resp = Http::timeout($timeout)->acceptJson()->get($url); // si tu servicio es GET, cambia a ->get($url)
		if ($resp->failed()) {
			return [
				'error'  => 'Failed to query AI result',
				'code'   => 502,
				'status' => $resp->status(),
				'body'   => $resp->body(),
			];
		}

		return $resp->json() ?? (json_decode($resp->body(), true) ?: []);
	}

	public function progressByProject(Request $request): JsonResponse
	{

		$data = $request->validate([
			'project_id' => ['required', 'integer', 'exists:projects,id'],
			// opcional: permitir override del target_url desde el front si quieres
			'target_url' => ['nullable', 'url'],
		]);

		/** @var \App\Models\Project $project */
		$project = Project::query()->findOrFail($data['project_id']);

		// Si no hay task en curso
		if (empty($project->ai_task_id)) {
			$project->ai_task_id = null;  // 👈 lo pones a null
			$project->ai_last_task_at = null;  // 👈 lo pones a null
			$project->save();             // 👈 lo persistes en la tabla
			return response()->json([
				'status'     => 'ok',
				'state'      => 'EMPTY',
				'message'    => 'Project has no active AI task',
				'project_id' => $project->id,
			]);
		}

		// target_url: del request o de la tabla datision_parameters
		$targetUrl = $data['target_url']
			?? (DB::table('datision_parameters')->value('machine_url') ?: null);

		if (!$targetUrl) {
			return response()->json([
				'message' => 'Missing target_url (datision_parameters.machine_url not found)',
			], 500);
		}

		// Consultar resultado
		$result = $this->queryAiResult($targetUrl, $project->ai_task_id, 30);

		if (isset($result['error'])) {
			return response()->json([
				'message' => $result['error'],
				'status'  => $result['status'] ?? null,
				'body'    => $result['body']   ?? null,
			], $result['code'] ?? 500);
		}

		$state = $result['state'] ?? null;

		if ($state === 'PROGRESS') {
			return response()->json([
				'status'     => 'ok',
				'state'      => 'PROGRESS',
				'percent'    => $result['status'] ?? null, // p.ej. "8.29%"
				'project_id' => $project->id,
			]);
		}

		if ($state === 'SUCCESS') {
			$project->ai_task_id = null;  // 👈 lo pones a null
			$project->ai_last_task_at = null;  // 👈 lo pones a null
			$project->save();             // 👈 lo persistes en la tabla
			return response()->json([
				'status'     => 'ok',
				'state'      => 'SUCCESS',
				'result'     => $result['result'] ?? null,
				'project_id' => $project->id,
			]);
		}

		// Estado raro o desconocido
		return response()->json([
			'status'     => 'ok',
			'state'      => $state ?? 'UNKNOWN',
			'raw'        => $result,
			'project_id' => $project->id,
		]);
	}
}