<?php

namespace App\Services;

use App\Enums\InventoryItemType;
use App\Enums\Taxonomy;
use App\Enums\ValueLevel;
use App\Models\AdvertisingOpportunity;
use App\Models\AnalysisRun;
use App\Models\Appearance;
use App\Models\AppearanceRelevance;
use App\Models\Brand;
use App\Models\ContextualRelationship;
use App\Models\Evidence;
use App\Models\InventoryItem;
use App\Models\Scene;
use App\Models\Taxon;
use App\Models\TaxonAssignment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WowDatasetImporter
{
    public const EXAMPLE_MARKER = 'EJEMPLO';

    /**
     * Elimina todos los datos de análisis de un proyecto (para reimportar desde cero).
     */
    public function reset(int $projectId): void
    {
        DB::transaction(function () use ($projectId) {
            $sceneIds = Scene::where('project_id', $projectId)->pluck('id');
            $itemIds = InventoryItem::where('project_id', $projectId)->pluck('id');
            $appearanceIds = Appearance::whereIn('scene_id', $sceneIds)->pluck('id');

            // Limpieza de tablas morf (sin cascade a nivel de BD).
            if ($appearanceIds->isNotEmpty()) {
                Evidence::where('evidenceable_type', Appearance::class)
                    ->whereIn('evidenceable_id', $appearanceIds)
                    ->delete();
            }

            if ($sceneIds->isNotEmpty() || $itemIds->isNotEmpty()) {
                TaxonAssignment::query()
                    ->where(function ($q) use ($sceneIds, $itemIds) {
                        $q->where(function ($q2) use ($sceneIds) {
                            $q2->where('assignable_type', Scene::class)->whereIn('assignable_id', $sceneIds);
                        })->orWhere(function ($q3) use ($itemIds) {
                            $q3->where('assignable_type', InventoryItem::class)->whereIn('assignable_id', $itemIds);
                        });
                    })
                    ->delete();
            }

            // El resto se borra por cascade de FK o por project_id.
            AdvertisingOpportunity::where('project_id', $projectId)->delete();
            ContextualRelationship::where('project_id', $projectId)->delete();
            AnalysisRun::where('project_id', $projectId)->delete();
            Scene::where('project_id', $projectId)->delete();
            InventoryItem::where('project_id', $projectId)->delete();
        });
    }

    /**
     * Importa el dataset curado de Alex (4 CSVs) en un proyecto.
     *
     * @param  array<string, string>  $files  ['scenes' => path, 'elements' => path, 'appearances' => path, 'opportunities' => path]
     * @return array<string, int> Conteos importados por tipo.
     */
    public function import(int $projectId, array $files, ?int $userId = null): array
    {
        $counts = ['scenes' => 0, 'elements' => 0, 'appearances' => 0, 'opportunities' => 0];

        DB::transaction(function () use ($projectId, $files, $userId, &$counts) {
            $sceneRefs = [];
            $elementRefs = [];
            $appearanceRefs = [];
            $taxonCache = [];

            $sceneId = function (string $ref) use (&$sceneRefs): int {
                return $this->refOrFail($sceneRefs, $ref, 'escena');
            };

            $elementId = function (string $ref) use (&$elementRefs): int {
                return $this->refOrFail($elementRefs, $ref, 'elemento');
            };

            $appearanceId = function (string $ref) use (&$appearanceRefs): int {
                return $this->refOrFail($appearanceRefs, $ref, 'aparición');
            };

            $contextTaxon = function (string $name) use (&$taxonCache): Taxon {
                $name = trim($name);
                if (! isset($taxonCache[$name])) {
                    $taxonCache[$name] = Taxon::firstOrCreate([
                        'taxonomy' => Taxonomy::KeyContext->value,
                        'name' => $name,
                    ]);
                }

                return $taxonCache[$name];
            };

            // 1. Scenes + key contexts
            foreach ($this->rows($files['scenes']) as $row) {
                $scene = Scene::create([
                    'project_id' => $projectId,
                    'position' => (int) ($row['position'] ?? 0),
                    'name' => $this->value($row, 'name'),
                    'start_time' => $this->parseTimecode($row['start_time'] ?? null),
                    'end_time' => $this->parseTimecode($row['end_time'] ?? null),
                ]);
                $sceneRefs[$this->value($row, 'scene_id_ref')] = $scene->id;
                $counts['scenes']++;

                foreach ($this->parseList($row['key_contexts'] ?? '') as $context) {
                    TaxonAssignment::create([
                        'taxon_id' => $contextTaxon($context)->id,
                        'assignable_type' => Scene::class,
                        'assignable_id' => $scene->id,
                        'created_by' => $userId,
                    ]);
                }
            }

            // 2. Elements (inventory_items)
            foreach ($this->rows($files['elements']) as $row) {
                $type = InventoryItemType::from(strtolower(trim($this->value($row, 'type'))));
                $brandName = trim($row['brand'] ?? '');
                if ($brandName === '' && $type === InventoryItemType::Brand) {
                    $brandName = $this->value($row, 'name');
                }

                $item = InventoryItem::create([
                    'project_id' => $projectId,
                    'name' => $this->value($row, 'name'),
                    'type' => $type,
                    'brand_id' => $brandName !== '' ? Brand::firstOrCreate(['name' => $brandName])->id : null,
                    'created_by' => $userId,
                ]);
                $elementRefs[$this->value($row, 'element_id_ref')] = $item->id;
                $counts['elements']++;
            }

            // 3. Appearances + relevances
            foreach ($this->rows($files['appearances']) as $row) {
                $appearance = Appearance::create([
                    'inventory_item_id' => $elementId($this->value($row, 'element_id_ref')),
                    'scene_id' => $sceneId($this->value($row, 'scene_id_ref')),
                    'start_time' => $this->parseTimecode($row['start_time'] ?? null),
                    'end_time' => $this->parseTimecode($row['end_time'] ?? null),
                    'pos_x' => $row['pos_x'] ?? 0,
                    'pos_y' => $row['pos_y'] ?? 0,
                    'w' => $row['w'] ?? 0,
                    'h' => $row['h'] ?? 0,
                    'source' => strtolower(trim($row['source'] ?? '')) ?: 'manual',
                    'created_by' => $userId,
                ]);
                $appearanceRefs[$this->value($row, 'appearance_id_ref')] = $appearance->id;
                $counts['appearances']++;

                foreach ($this->parseList($row['relevant_for'] ?? '') as $vertical) {
                    AppearanceRelevance::create([
                        'appearance_id' => $appearance->id,
                        'vertical' => strtolower($vertical),
                        'created_by' => $userId,
                    ]);
                }
            }

            // 4. Advertising opportunities + elements + taxons
            foreach ($this->rows($files['opportunities']) as $row) {
                $opportunity = AdvertisingOpportunity::create([
                    'project_id' => $projectId,
                    'scene_id' => isset($row['scene_id_ref']) && $row['scene_id_ref'] !== '' ? $sceneId($row['scene_id_ref']) : null,
                    'appearance_id' => isset($row['appearance_id_ref']) && $row['appearance_id_ref'] !== '' ? $appearanceId($row['appearance_id_ref']) : null,
                    'value_level' => ValueLevel::from(strtolower(trim($this->value($row, 'value_level')))),
                    'rationale' => $row['rationale'] ?? null,
                    'created_by' => $userId,
                ]);

                $opportunity->elements()->attach(
                    array_map($elementId, $this->parseList($row['elements_involved'] ?? ''))
                );

                $opportunity->taxons()->attach(
                    array_map(fn ($name) => $contextTaxon($name)->id, $this->parseList($row['contexts'] ?? ''))
                );

                $counts['opportunities']++;
            }
        });

        return $counts;
    }

    /**
     * Lee un CSV y devuelve filas asociativas (header => valor), saltando
     * la cabecera, las filas vacías y las filas de EJEMPLO.
     *
     * @return array<int, array<string, string>>
     */
    private function rows(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir el CSV: {$path}");
        }

        $header = fgetcsv($handle, null, ',', '"', '\\');
        if ($header === false) {
            fclose($handle);

            return [];
        }
        $header = array_map('trim', $header);

        $rows = [];
        while (($raw = fgetcsv($handle, null, ',', '"', '\\')) !== false) {
            if ($raw === [null]) {
                continue;
            }
            if (count($raw) !== count($header) && count($raw) === 1 && trim((string) $raw[0]) === '') {
                continue;
            }
            $row = [];
            foreach ($header as $i => $key) {
                $row[$key] = $raw[$i] ?? '';
            }
            if ($this->isExample($row)) {
                continue;
            }
            if (! array_filter(array_map('trim', array_values($row)))) {
                continue;
            }
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    private function isExample(array $row): bool
    {
        foreach ($row as $value) {
            if (str_contains(mb_strtoupper((string) $value), self::EXAMPLE_MARKER)) {
                return true;
            }
        }

        return false;
    }

    private function value(array $row, string $key): string
    {
        return trim((string) ($row[$key] ?? ''));
    }

    /**
     * Convierte un timecode SRT `HH:MM:SS,mmm` (o con punto) a milisegundos.
     */
    private function parseTimecode(?string $value): int
    {
        $value = trim((string) $value);
        if ($value === '') {
            throw new RuntimeException('Timecode vacío en el CSV.');
        }
        if (! preg_match('/^(\d{1,2}):(\d{2}):(\d{2})(?:[,.](\d{1,3}))?$/', $value, $m)) {
            throw new RuntimeException("Timecode inválido: {$value}");
        }

        return ((int) $m[1] * 3600 + (int) $m[2] * 60 + (int) $m[3]) * 1000 + (int) ($m[4] ?? 0);
    }

    /**
     * @return array<int, string>
     */
    private function parseList(?string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $value))));
    }

    private function refOrFail(array $refs, string $ref, string $label): int
    {
        if (! isset($refs[$ref])) {
            throw new RuntimeException("Referencia de {$label} no encontrada: {$ref}");
        }

        return $refs[$ref];
    }
}
