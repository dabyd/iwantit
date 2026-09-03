<?php

namespace App\Http\Controllers;

use App\Helpers\ProjectPermissionHelper;
use App\Models\Project;
use App\Services\WowAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function __construct(private readonly WowAnalysisService $analysis) {}

    public function overview(Project $project): JsonResponse
    {
        $this->authorizeView($project);

        return response()->json($this->analysis->overview($project));
    }

    public function advertisingOpportunities(Project $project, Request $request): JsonResponse
    {
        $this->authorizeView($project);

        $level = $request->query('level');
        if ($level !== null && ! in_array($level, ['high', 'medium', 'low'], true)) {
            return response()->json(['message' => 'Nivel inválido. Valores permitidos: high, medium, low.'], 422);
        }

        return response()->json($this->analysis->advertisingOpportunities($project, $level));
    }

    private function authorizeView(Project $project): void
    {
        $user = auth()->user();

        abort_unless($user && ProjectPermissionHelper::canView($user, $project), 403);
    }
}
