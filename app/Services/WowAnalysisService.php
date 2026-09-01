<?php

namespace App\Services;

use App\Enums\Taxonomy;
use App\Models\AdvertisingOpportunity;
use App\Models\Appearance;
use App\Models\AppearanceRelevance;
use App\Models\ContextualRelationship;
use App\Models\InventoryItem;
use App\Models\Project;
use App\Models\Scene;
use App\Models\TaxonAssignment;
use Illuminate\Support\Collection;

class WowAnalysisService
{
    /**
     * Agregados para la pantalla Analysis Overview.
     *
     * @return array<string, mixed>
     */
    public function overview(Project $project): array
    {
        return [
            'content_intelligence' => $this->contentIntelligence($project),
            'business_opportunities' => $this->businessOpportunities($project),
            'key_contexts' => $this->keyContexts($project),
        ];
    }

    /**
     * Listado de oportunidades de Advertising (opcionalmente filtrado por nivel).
     *
     * @return array{items: array<int, array<string, mixed>>}
     */
    public function advertisingOpportunities(Project $project, ?string $level = null): array
    {
        $query = AdvertisingOpportunity::where('project_id', $project->id)
            ->with(['scene:id,name,start_time,end_time', 'elements:id,name,type', 'taxons:id,name', 'appearance:id,start_time,end_time']);

        if ($level !== null) {
            $query->where('value_level', $level);
        }

        $opportunities = $query->orderByRaw('FIELD(value_level, "high", "medium", "low")')
            ->orderBy('id')
            ->get();

        $timeOnScreen = $this->elementTimeOnScreenMap(
            $opportunities->flatMap->elements->pluck('id')->unique()
        );

        $items = $opportunities
            ->map(fn (AdvertisingOpportunity $opportunity) => $this->formatOpportunity($opportunity, $timeOnScreen))
            ->values()
            ->all();

        return ['items' => $items];
    }

    /**
     * Time-on-screen (ms) por elemento: unión temporal de sus apariciones.
     *
     * @return array<int, int> [inventory_item_id => ms]
     */
    private function elementTimeOnScreenMap(Collection $itemIds): array
    {
        if ($itemIds->isEmpty()) {
            return [];
        }

        return Appearance::whereIn('inventory_item_id', $itemIds)
            ->get(['inventory_item_id', 'start_time', 'end_time'])
            ->groupBy('inventory_item_id')
            ->map(fn (Collection $group) => $this->unionDurationMs(
                $group->map(fn (Appearance $a) => [$a->start_time, $a->end_time])
            ))
            ->all();
    }

    /**
     * Duración total (ms) de la unión de intervalos [start, end), sin doble conteo de solapamientos.
     *
     * @param  Collection<int, array{0: int|null, 1: int|null}>  $intervals
     */
    private function unionDurationMs(Collection $intervals): int
    {
        $sorted = $intervals
            ->filter(fn ($i) => $i[0] !== null && $i[1] !== null)
            ->map(fn ($i) => [(int) $i[0], (int) $i[1]])
            ->sortBy(fn ($i) => $i[0])
            ->values();

        if ($sorted->isEmpty()) {
            return 0;
        }

        $total = 0;
        $start = $sorted[0][0];
        $end = $sorted[0][1];

        foreach ($sorted->slice(1) as [$s, $e]) {
            if ($s <= $end) {
                $end = max($end, $e);
            } else {
                $total += $end - $start;
                $start = $s;
                $end = $e;
            }
        }

        return $total + ($end - $start);
    }

    /**
     * @return array<string, int>
     */
    private function contentIntelligence(Project $project): array
    {
        return [
            'scenes' => Scene::where('project_id', $project->id)->count(),
            'elements' => InventoryItem::where('project_id', $project->id)->count(),
            'appearances' => Appearance::whereHas('scene', fn ($q) => $q->where('project_id', $project->id))->count(),
            'relationships' => ContextualRelationship::where('project_id', $project->id)->count(),
        ];
    }

    /**
     * @return array<string, array<string, int>|int>
     */
    private function businessOpportunities(Project $project): array
    {
        $advertising = AdvertisingOpportunity::where('project_id', $project->id)
            ->selectRaw('value_level, COUNT(*) as total')
            ->groupBy('value_level')
            ->pluck('total', 'value_level');

        $clearance = AppearanceRelevance::where('vertical', 'clearance')
            ->whereHas('appearance.scene', fn ($q) => $q->where('project_id', $project->id))
            ->count();

        return [
            'advertising' => [
                'high' => (int) ($advertising['high'] ?? 0),
                'medium' => (int) ($advertising['medium'] ?? 0),
                'low' => (int) ($advertising['low'] ?? 0),
            ],
            'clearance_relevant' => $clearance,
        ];
    }

    /**
     * @return array<int, array{name: string, scenes: int}>
     */
    private function keyContexts(Project $project): array
    {
        $sceneIds = Scene::where('project_id', $project->id)->pluck('id');

        return TaxonAssignment::where('assignable_type', Scene::class)
            ->whereIn('assignable_id', $sceneIds)
            ->whereHas('taxon', fn ($q) => $q->where('taxonomy', Taxonomy::KeyContext->value))
            ->with('taxon:id,name')
            ->get()
            ->groupBy('taxon_id')
            ->map(fn ($group) => [
                'name' => $group->first()->taxon->name,
                'scenes' => $group->pluck('assignable_id')->unique()->count(),
            ])
            ->sortByDesc('scenes')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $timeOnScreen  [inventory_item_id => ms]
     * @return array<string, mixed>
     */
    private function formatOpportunity(AdvertisingOpportunity $opportunity, array $timeOnScreen = []): array
    {
        [$startMs, $endMs] = $this->opportunityTimeSpan($opportunity);

        return [
            'id' => $opportunity->id,
            'value_level' => $opportunity->value_level->value,
            'scene' => $opportunity->scene ? ['id' => $opportunity->scene->id, 'name' => $opportunity->scene->name] : null,
            'elements' => $opportunity->elements->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->type->value,
                'time_on_screen_ms' => $timeOnScreen[$item->id] ?? 0,
            ])->values()->all(),
            'contexts' => $opportunity->taxons->pluck('name')->all(),
            'rationale' => $opportunity->rationale,
            'start_ms' => $startMs,
            'end_ms' => $endMs,
            'duration_ms' => ($startMs !== null && $endMs !== null) ? $endMs - $startMs : null,
        ];
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function opportunityTimeSpan(AdvertisingOpportunity $opportunity): array
    {
        if ($opportunity->appearance) {
            return [$opportunity->appearance->start_time, $opportunity->appearance->end_time];
        }

        if ($opportunity->scene) {
            return [$opportunity->scene->start_time, $opportunity->scene->end_time];
        }

        return [null, null];
    }
}
