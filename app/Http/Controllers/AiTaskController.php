<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Project;
use Illuminate\Validation\ValidationException;

class AiTaskController extends Controller
{
    /**
     * Guarda el task_id de IA en projects.
     *
     * Request JSON:
     * {
     *   "project_id": int,
     *   "task_id": "uuid|string"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'task_id'    => ['required', 'string', 'max:191'],
        ]);

        /** @var \App\Models\Project $project */
        $project = Project::query()->findOrFail($data['project_id']);

        // Ajusta el/los campos según tu esquema real:
        // Supongo columna 'ai_task_id' en projects. Si no existe, ver migración opcional abajo.
        $project->ai_task_id = $data['task_id'];
        // Opcional: marca timestamp de última tarea
        if ($project->isFillable('ai_last_task_at') || \Schema::hasColumn('projects', 'ai_last_task_at')) {
            $project->ai_last_task_at = now();
        }

        $project->save();

        return response()->json([
            'status'     => 'ok',
            'project_id' => $project->id,
            'task_id'    => $project->ai_task_id,
        ]);
    }
}