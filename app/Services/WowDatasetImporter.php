<?php

namespace App\Services;

use App\Enums\AppearanceSource;
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

            $sceneId = function (string $ref, string $file, int $line, string $col) use (&$sceneRefs): int {
                return $this->refOrFail($sceneRefs, $ref, 'escena', $file, $line, $col);
            };

            $elementId = function (string $ref, string $file, int $line, string $col) use (&$elementRefs): int {
                return $this->refOrFail($elementRefs, $ref, 'elemento', $file, $line, $col);
            };

            $appearanceId = function (string $ref, string $file, int $line, string $col) use (&$appearanceRefs): int {
                return $this->refOrFail($appearanceRefs, $ref, 'aparición', $file, $line, $col);
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
            foreach ($this->rowsWithMeta($files['scenes']) as $meta) {
                $row = $meta['row'];
                $line = $meta['line'];
                $file = $meta['file'];
                $shortFile = basename($file);

                $sceneRef = $this->requireValue($row, 'scene_id_ref', $shortFile, $line);
                $name = $this->requireValue($row, 'name', $shortFile, $line);

                $scene = Scene::create([
                    'project_id' => $projectId,
                    'position' => (int) ($row['position'] ?? 0),
                    'name' => $name,
                    'start_time' => $this->parseTimecode($row['start_time'] ?? null, $shortFile, $line, 'start_time'),
                    'end_time' => $this->parseTimecode($row['end_time'] ?? null, $shortFile, $line, 'end_time'),
                ]);
                $sceneRefs[$sceneRef] = $scene->id;
                $counts['scenes']++;

                foreach ($this->collectKeyContexts($row) as $context) {
                    TaxonAssignment::create([
                        'taxon_id' => $contextTaxon($context)->id,
                        'assignable_type' => Scene::class,
                        'assignable_id' => $scene->id,
                        'created_by' => $userId,
                    ]);
                }
            }

            // 2. Elements (inventory_items)
            foreach ($this->rowsWithMeta($files['elements']) as $meta) {
                $row = $meta['row'];
                $line = $meta['line'];
                $file = $meta['file'];
                $shortFile = basename($file);

                $elementRef = $this->requireValue($row, 'element_id_ref', $shortFile, $line);
                $name = $this->requireValue($row, 'name', $shortFile, $line);
                $rawType = $this->requireValue($row, 'type', $shortFile, $line);
                $typeValue = $this->mapInventoryType($rawType, $shortFile, $line, 'type');
                try {
                    $type = InventoryItemType::from($typeValue);
                } catch (\ValueError $e) {
                    throw new RuntimeException("{$shortFile} línea {$line} columna type: tipo inválido \"{$rawType}\" (normalizado: \"{$typeValue}\"). Valores permitidos: ".implode(', ', array_map(fn ($c) => $c->value, InventoryItemType::cases()))." — fila: ".json_encode($row, JSON_UNESCAPED_UNICODE));
                }

                $brandName = trim($row['brand'] ?? '');
                if ($brandName === '' && $type === InventoryItemType::Brand) {
                    $brandName = $name;
                }

                $item = InventoryItem::create([
                    'project_id' => $projectId,
                    'name' => $name,
                    'type' => $type,
                    'brand_id' => $brandName !== '' ? Brand::firstOrCreate(['name' => $brandName])->id : null,
                    'created_by' => $userId,
                ]);
                $elementRefs[$elementRef] = $item->id;
                $counts['elements']++;
            }

            // 3. Appearances + relevances
            foreach ($this->rowsWithMeta($files['appearances']) as $meta) {
                $row = $meta['row'];
                $line = $meta['line'];
                $file = $meta['file'];
                $shortFile = basename($file);

                $appearanceRef = $this->requireValue($row, 'appearance_id_ref', $shortFile, $line);
                $elementRefVal = $this->requireValue($row, 'element_id_ref', $shortFile, $line);
                $sceneRefVal = $this->requireValue($row, 'scene_id_ref', $shortFile, $line);

                $sourceRaw = strtolower(trim($row['source'] ?? ''));
                $sourceMapped = $this->mapAppearanceSource($sourceRaw);
                try {
                    $sourceEnum = AppearanceSource::from($sourceMapped);
                } catch (\ValueError $e) {
                    throw new RuntimeException("{$shortFile} línea {$line} columna source: valor inválido \"{$sourceRaw}\" (mapeado: \"{$sourceMapped}\"). Valores permitidos: ".implode(', ', array_map(fn ($c) => $c->value, AppearanceSource::cases()))." — fila: ".json_encode($row, JSON_UNESCAPED_UNICODE));
                }

                $appearance = Appearance::create([
                    'inventory_item_id' => $elementId($elementRefVal, $shortFile, $line, 'element_id_ref'),
                    'scene_id' => $sceneId($sceneRefVal, $shortFile, $line, 'scene_id_ref'),
                    'start_time' => $this->parseTimecode($row['start_time'] ?? null, $shortFile, $line, 'start_time'),
                    'end_time' => $this->parseTimecode($row['end_time'] ?? null, $shortFile, $line, 'end_time'),
                    'pos_x' => $this->parseCoord($row['pos_x'] ?? null),
                    'pos_y' => $this->parseCoord($row['pos_y'] ?? null),
                    'w' => $this->parseCoord($row['w'] ?? null),
                    'h' => $this->parseCoord($row['h'] ?? null),
                    'source' => $sourceEnum->value,
                    'provenance' => isset($row['provenance']) && trim((string) $row['provenance']) !== '' ? trim((string) $row['provenance']) : null,
                    'created_by' => $userId,
                ]);
                $appearanceRefs[$appearanceRef] = $appearance->id;
                $counts['appearances']++;

                foreach ($this->collectRelevantFor($row) as $vertical) {
                    $verticalNorm = strtolower(trim($vertical));
                    if ($verticalNorm === '') {
                        continue;
                    }
                    try {
                        AppearanceRelevance::create([
                            'appearance_id' => $appearance->id,
                            'vertical' => $verticalNorm,
                            'created_by' => $userId,
                        ]);
                    } catch (\Throwable $e) {
                        throw new RuntimeException("{$shortFile} línea {$line} columna relevant_for: vertical inválida \"{$vertical}\" (normalizado: \"{$verticalNorm}\") — fila: ".json_encode($row, JSON_UNESCAPED_UNICODE)." — error: ".$e->getMessage());
                    }
                }
            }

            // 4. Advertising opportunities / contexts
            foreach ($this->rowsWithMeta($files['opportunities']) as $meta) {
                $row = $meta['row'];
                $line = $meta['line'];
                $file = $meta['file'];
                $shortFile = basename($file);

                // Detect format: new uses context + context_quality + scene_ref_1; old uses value_level + scene_id_ref
                $isNewFormat = array_key_exists('context', $row) || array_key_exists('context_quality', $row) || array_key_exists('scene_ref_1', $row);

                $valueLevelRaw = trim((string) ($row['value_level'] ?? $row['context_quality'] ?? ''));
                if ($valueLevelRaw === '') {
                    throw new RuntimeException("{$shortFile} línea {$line} columna value_level/context_quality: valor vacío. Se esperaba high/medium/low. Fila: ".json_encode($row, JSON_UNESCAPED_UNICODE));
                }
                try {
                    $valueLevel = ValueLevel::from(strtolower($valueLevelRaw));
                } catch (\ValueError $e) {
                    throw new RuntimeException("{$shortFile} línea {$line} columna value_level/context_quality: valor inválido \"{$valueLevelRaw}\". Valores permitidos: high, medium, low — fila: ".json_encode($row, JSON_UNESCAPED_UNICODE));
                }

                $rationale = isset($row['rationale']) && trim((string) $row['rationale']) !== '' ? trim((string) $row['rationale']) : null;

                if ($isNewFormat) {
                    // New: context (single), scene_ref_1..3, elements_involved semicolon-separated with names
                    $contexts = [];
                    if (isset($row['context']) && trim((string) $row['context']) !== '') {
                        $contexts[] = trim((string) $row['context']);
                    }
                    // fallback to legacy contexts column if present
                    if (isset($row['contexts']) && trim((string) $row['contexts']) !== '') {
                        $contexts = array_merge($contexts, $this->parseList($row['contexts']));
                    }

                    $sceneRefsForRow = [];
                    foreach (['scene_ref_1', 'scene_ref_2', 'scene_ref_3', 'scene_id_ref'] as $k) {
                        if (isset($row[$k]) && trim((string) $row[$k]) !== '') {
                            $sceneRefsForRow[] = trim((string) $row[$k]);
                        }
                    }

                    $elementTokens = $this->parseElementsInvolved($row['elements_involved'] ?? '');

                    if (empty($sceneRefsForRow)) {
                        // No scene refs → create single opportunity without scene (but with context/taxons)
                        $opportunity = AdvertisingOpportunity::create([
                            'project_id' => $projectId,
                            'scene_id' => null,
                            'appearance_id' => null,
                            'value_level' => $valueLevel,
                            'rationale' => $rationale,
                            'created_by' => $userId,
                        ]);
                        $this->attachElementsAndContexts($opportunity, $elementTokens, $contexts, $elementId, $contextTaxon, $shortFile, $line);
                        $counts['opportunities']++;
                    } else {
                        // Create one opportunity per scene_ref (preserves representative scenes)
                        foreach ($sceneRefsForRow as $sceneRefToken) {
                            $opportunity = AdvertisingOpportunity::create([
                                'project_id' => $projectId,
                                'scene_id' => $sceneId($sceneRefToken, $shortFile, $line, 'scene_ref_*'),
                                'appearance_id' => null,
                                'value_level' => $valueLevel,
                                'rationale' => $rationale,
                                'created_by' => $userId,
                            ]);
                            $this->attachElementsAndContexts($opportunity, $elementTokens, $contexts, $elementId, $contextTaxon, $shortFile, $line);
                            $counts['opportunities']++;
                        }
                    }
                } else {
                    // Legacy format
                    $sceneIdVal = isset($row['scene_id_ref']) && trim((string) $row['scene_id_ref']) !== '' ? $sceneId(trim((string) $row['scene_id_ref']), $shortFile, $line, 'scene_id_ref') : null;
                    $appearanceIdVal = isset($row['appearance_id_ref']) && trim((string) $row['appearance_id_ref']) !== '' ? $appearanceId(trim((string) $row['appearance_id_ref']), $shortFile, $line, 'appearance_id_ref') : null;

                    $opportunity = AdvertisingOpportunity::create([
                        'project_id' => $projectId,
                        'scene_id' => $sceneIdVal,
                        'appearance_id' => $appearanceIdVal,
                        'value_level' => $valueLevel,
                        'rationale' => $rationale,
                        'created_by' => $userId,
                    ]);

                    // Elements: legacy comma-separated E refs
                    $elementTokensLegacy = isset($row['elements_involved']) ? $this->parseList($row['elements_involved']) : [];
                    // also handle semicolon case gracefully
                    if (count($elementTokensLegacy) === 1 && str_contains($elementTokensLegacy[0], ';')) {
                        $elementTokensLegacy = $this->parseElementsInvolved($row['elements_involved']);
                    }
                    $contextsLegacy = isset($row['contexts']) ? $this->parseList($row['contexts']) : [];

                    $this->attachElementsAndContexts($opportunity, $elementTokensLegacy, $contextsLegacy, $elementId, $contextTaxon, $shortFile, $line);
                    $counts['opportunities']++;
                }
            }
        });

        return $counts;
    }

    private function attachElementsAndContexts(AdvertisingOpportunity $opportunity, array $elementTokens, array $contexts, callable $elementIdResolver, callable $contextTaxon, string $shortFile, int $line): void
    {
        if (! empty($elementTokens)) {
            $ids = [];
            foreach ($elementTokens as $tok) {
                $ref = $this->extractElementRef($tok);
                if ($ref === null) {
                    continue;
                }
                try {
                    $ids[] = $elementIdResolver($ref, $shortFile, $line, 'elements_involved');
                } catch (RuntimeException $e) {
                    // rethrow with extra context about token
                    throw new RuntimeException($e->getMessage()." (token original: \"{$tok}\")");
                }
            }
            if (! empty($ids)) {
                $opportunity->elements()->attach(array_unique($ids));
            }
        }

        if (! empty($contexts)) {
            $taxonIds = [];
            foreach ($contexts as $ctx) {
                $ctx = trim((string) $ctx);
                if ($ctx === '') {
                    continue;
                }
                $taxonIds[] = $contextTaxon($ctx)->id;
            }
            if (! empty($taxonIds)) {
                $opportunity->taxons()->attach(array_unique($taxonIds));
            }
        }
    }

    private function extractElementRef(string $token): ?string
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }
        // Supports "E009 Restaurant Terra Nera" or "E001" etc.
        if (preg_match('/^(E\d+)/i', $token, $m)) {
            return strtoupper($m[1]);
        }

        return null;
    }

    /**
     * Parsea elements_involved que puede venir como "E009 Name; E010 Name" o "E01, E02"
     * @return array<int, string>
     */
    private function parseElementsInvolved(?string $value): array
    {
        $value = trim((string) $value);
        if ($value === '') {
            return [];
        }
        // Prefer semicolon, then comma
        if (str_contains($value, ';')) {
            $parts = explode(';', $value);
        } else {
            $parts = explode(',', $value);
        }

        return array_values(array_filter(array_map('trim', $parts)));
    }

    /**
     * Lee un CSV y devuelve filas asociativas con metadatos (line, file, row),
     * detectando la fila de cabecera real (salta filas de título).
     *
     * @return array<int, array{line:int, file:string, row:array<string,string>}>
     */
    private function rowsWithMeta(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir el CSV: {$path}");
        }

        $allRows = [];
        $lineNum = 0;
        while (($raw = fgetcsv($handle, null, ',', '"', '\\')) !== false) {
            $lineNum++;
            $allRows[] = ['line' => $lineNum, 'raw' => $raw];
        }
        fclose($handle);

        if (empty($allRows)) {
            return [];
        }

        // Detect header row: first row containing a known primary key column name.
        $knownKeys = ['scene_id_ref', 'element_id_ref', 'appearance_id_ref', 'opportunity_id_ref', 'advertising_context_id_ref', 'advertising_context_id_ref', 'scene_ref_1', 'context'];
        $headerIndex = null;
        $header = null;
        foreach ($allRows as $idx => $entry) {
            $raw = $entry['raw'];
            if ($raw === [null] || $raw === false) {
                continue;
            }
            $normalized = array_map(fn ($v) => trim((string) $v), $raw);
            // check if any normalized cell exactly matches a known key
            foreach ($normalized as $cell) {
                if (in_array($cell, $knownKeys, true)) {
                    $headerIndex = $idx;
                    $header = $normalized;
                    break 2;
                }
            }
            // also detect header by presence of position+name combo or type+brand
            $lower = array_map('strtolower', $normalized);
            if (in_array('position', $lower, true) && in_array('name', $lower, true)) {
                $headerIndex = $idx;
                $header = $normalized;
                break;
            }
            if (in_array('type', $lower, true) && in_array('family', $lower, true)) {
                $headerIndex = $idx;
                $header = $normalized;
                break;
            }
        }

        if ($headerIndex === null) {
            // Fallback: assume first non-empty row is header (legacy behavior)
            foreach ($allRows as $idx => $entry) {
                $raw = $entry['raw'];
                $normalized = array_map(fn ($v) => trim((string) $v), $raw);
                if (array_filter($normalized, fn ($v) => $v !== '')) {
                    $headerIndex = $idx;
                    $header = $normalized;
                    break;
                }
            }
        }

        if ($headerIndex === null || $header === null) {
            return [];
        }

        // Remove BOM from first header cell if present
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $result = [];
        for ($i = $headerIndex + 1; $i < count($allRows); $i++) {
            $entry = $allRows[$i];
            $raw = $entry['raw'];
            $line = $entry['line'];
            if ($raw === [null]) {
                continue;
            }
            if (count($raw) === 1 && trim((string) $raw[0]) === '') {
                continue;
            }
            $row = [];
            foreach ($header as $hi => $key) {
                if ($key === '') {
                    continue;
                }
                $row[$key] = $raw[$hi] ?? '';
            }
            if ($this->isExample($row)) {
                continue;
            }
            if (! array_filter(array_map('trim', array_values($row)))) {
                continue;
            }
            $result[] = ['line' => $line, 'file' => $path, 'row' => $row];
        }

        return $result;
    }

    /**
     * Lee un CSV y devuelve filas asociativas (header => valor), saltando
     * la cabecera, las filas vacías y las filas de EJEMPLO.
     * @deprecated use rowsWithMeta
     * @return array<int, array<string, string>>
     */
    private function rows(string $path): array
    {
        return array_map(fn ($m) => $m['row'], $this->rowsWithMeta($path));
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

    private function requireValue(array $row, string $key, string $file, int $line): string
    {
        $val = trim((string) ($row[$key] ?? ''));
        if ($val === '') {
            throw new RuntimeException("{$file} línea {$line} columna {$key}: valor vacío/obligatorio. Fila: ".json_encode($row, JSON_UNESCAPED_UNICODE));
        }

        return $val;
    }

    private function value(array $row, string $key): string
    {
        return trim((string) ($row[$key] ?? ''));
    }

    private function collectKeyContexts(array $row): array
    {
        $out = [];
        if (isset($row['key_contexts']) && trim((string) $row['key_contexts']) !== '') {
            $out = array_merge($out, $this->parseList($row['key_contexts']));
        }
        foreach (['key_context_1', 'key_context_2', 'key_context_3'] as $k) {
            if (isset($row[$k]) && trim((string) $row[$k]) !== '') {
                $out[] = trim((string) $row[$k]);
            }
        }

        return array_values(array_unique(array_filter($out)));
    }

    private function collectRelevantFor(array $row): array
    {
        $out = [];
        if (isset($row['relevant_for']) && trim((string) $row['relevant_for']) !== '') {
            $out = array_merge($out, $this->parseList($row['relevant_for']));
        }
        foreach (['relevant_for_1', 'relevant_for_2', 'relevant_for_3'] as $k) {
            if (isset($row[$k]) && trim((string) $row[$k]) !== '') {
                $out[] = trim((string) $row[$k]);
            }
        }

        return array_values(array_unique(array_filter(array_map('trim', $out))));
    }

    private function mapInventoryType(string $raw, string $file, int $line, string $col): string
    {
        $lower = strtolower(trim($raw));
        $map = [
            'place' => 'location',
            'places' => 'location',
            'location' => 'location',
        ];

        return $map[$lower] ?? $lower;
    }

    private function mapAppearanceSource(string $lower): string
    {
        if ($lower === '' || $lower === 'imported') {
            return 'manual';
        }

        return $lower;
    }

    private function parseCoord($value): float
    {
        $v = trim((string) ($value ?? ''));
        if ($v === '') {
            return 0;
        }

        return (float) $v;
    }

    /**
     * Convierte un timecode SRT `HH:MM:SS,mmm` (o con punto, o con `:` para frames) a milisegundos.
     * Soporta:
     * - HH:MM:SS,mmm  (SRT clásico)
     * - HH:MM:SS.mmm  (dot)
     * - HH:MM:SS      (sin ms)
     * - HH:MM:SS:FF   (frames, FF se convierte a ms aprox: FF * 1000/25 ≈ FF*40)
     */
    private function parseTimecode(?string $value, ?string $file = null, ?int $line = null, ?string $column = null): int
    {
        $raw = $value;
        $value = trim((string) $value);
        $ctx = $file !== null ? " ({$file}".($line !== null ? " línea {$line}" : "").($column !== null ? " columna {$column}" : "")." valor=\"{$raw}\")" : "";
        if ($value === '') {
            throw new RuntimeException("Timecode vacío en el CSV{$ctx}. Fila: valor=\"{$raw}\". Revisa que la columna {$column} tenga formato HH:MM:SS.mmm o HH:MM:SS:FF.");
        }

        // Case HH:MM:SS:FF (4 parts colon separated)
        if (preg_match('/^(\d{1,2}):(\d{2}):(\d{2}):(\d{1,3})$/', $value, $m)) {
            $hh = (int) $m[1];
            $mm = (int) $m[2];
            $ss = (int) $m[3];
            $ff = (int) $m[4];
            // frames to ms: assume 25fps → 40ms per frame; cap at 999
            $ms = min(999, $ff * 40);
            return (($hh * 3600) + ($mm * 60) + $ss) * 1000 + $ms;
        }

        if (preg_match('/^(\d{1,2}):(\d{2}):(\d{2})(?:[,.](\d{1,3}))?$/', $value, $m)) {
            $hh = (int) $m[1];
            $mm = (int) $m[2];
            $ss = (int) $m[3];
            $msRaw = $m[4] ?? '0';
            // normalize ms to 3 digits: "7" → "700", "70" → "700"? actually pad right
            $msPadded = str_pad($msRaw, 3, '0', STR_PAD_RIGHT);
            // if original was 1-2 digits with dot like .7 we treat as 700; but SRT spec uses 3 digits always
            $ms = (int) substr($msPadded, 0, 3);

            return (($hh * 3600) + ($mm * 60) + $ss) * 1000 + $ms;
        }

        throw new RuntimeException("Timecode inválido{$ctx}: \"{$value}\". Formatos válidos: HH:MM:SS,mmm  HH:MM:SS.mmm  HH:MM:SS  HH:MM:SS:FF (frames).");
    }

    /**
     * @return array<int, string>
     */
    private function parseList(?string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $value))));
    }

    private function refOrFail(array $refs, string $ref, string $label, ?string $file = null, ?int $line = null, ?string $col = null): int
    {
        if (! isset($refs[$ref])) {
            $ctx = $file !== null ? " ({$file}".($line !== null ? " línea {$line}" : "").($col !== null ? " columna {$col}" : "").")" : "";
            throw new RuntimeException("Referencia de {$label} no encontrada{$ctx}: {$ref}. Disponibles: ".implode(', ', array_keys($refs)));
        }

        return $refs[$ref];
    }
}
