<?php

namespace App\Http\Controllers;

use App\Helpers\IaProducts;
use App\Models\AiImportBatch;
use App\Models\Brand;
use App\Models\Datision;
use App\Models\DatisionDetection;
use App\Models\DatisionObjectsIaClass;
use App\Models\Hotpoint;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DatisionController extends Controller
{
    /**
     * Display the main Datision index view.
     */
    public function index(): View
    {
        $projects = $this->getProjects();

        // Paginamos al final
        $projects = $projects->distinct()->paginate(300);
        $controller = $this;

        $territorios = DB::table('territories')->get();
        $terr = [];
        foreach ($territorios->toArray() as $territory) {
            $terr[$territory->id] = ['id' => $territory->id, 'name' => $territory->name];
        }

        return view('projects.index', compact('projects', 'controller', 'terr'))->with('i', (request()->input('page', 1) - 1) * 300);
    }

    /**
     * Handle the incoming Datision upgrade request.
     */
    public function upgrade(Request $request): JsonResponse
    {
        $data = $request->all();

        // Contadores
        $counters = [
            'projects_created' => 0,
            'objects_created' => 0,
            'objects_updated' => 0,
            'detections_created' => 0,
            'detections_updated' => 0,
            'detections_deleted' => 0,
        ];

        // 1. Buscar o crear el proyecto
        $datision = Datision::where('id_project', $data['id_project'])->first();
        if (! $datision) {
            $datision = Datision::create(['id_project' => $data['id_project']]);
            $counters['projects_created']++;
        }

        foreach ($data['results'] as $resultData) {
            $existingResult = $datision->results()->where('id_object', $resultData['id_object'])->first();

            if ($existingResult) {
                if ($existingResult->class !== $resultData['class']) {
                    $existingResult->class = $resultData['class'];
                    $existingResult->save();
                    $deletedCount = $existingResult->detections()->count();
                    $existingResult->detections()->delete();
                    $counters['detections_deleted'] += $deletedCount;
                    $counters['objects_updated']++;
                }
            } else {
                $existingResult = $datision->results()->create([
                    'id_object' => $resultData['id_object'],
                    'class' => $resultData['class'],
                ]);
                $counters['objects_created']++;
            }

            foreach ($resultData['detections'] as $detection) {
                [$frame, $x1, $y1, $x2, $y2] = $detection;

                $existingDetection = $existingResult->detections()
                    ->where('frame', $frame)
                    ->first();

                if ($existingDetection) {
                    $existingDetection->update([
                        'x1' => $x1,
                        'y1' => $y1,
                        'x2' => $x2,
                        'y2' => $y2,
                    ]);
                    $counters['detections_updated']++;
                } else {
                    $existingResult->detections()->create([
                        'frame' => $frame,
                        'x1' => $x1,
                        'y1' => $y1,
                        'x2' => $x2,
                        'y2' => $y2,
                    ]);
                    $counters['detections_created']++;
                }
            }
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Datos procesados correctamente.',
            'summary' => $counters,
        ]);
    }

    /**
     * Provide a CSRF token for external services.
     */
    public function getCsrfToken(Request $request): JsonResponse
    {
        return response()->json(['csrf_token' => csrf_token()]);
    }

    /**
     * Get all projects with their relationships.
     */
    public function getProjects(): JsonResponse
    {
        // Método original del segundo controlador
        $projects = Project::with(['projects_users', 'users'])->get();

        return response()->json([
            'status' => 'ok',
            'projects' => $projects,
        ]);
    }

    /**
     * Get objects for a specific project.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public static function getProjectObjects($project_id)
    {
        $project = Datision::where('id_project', $project_id)->first();
        $ret = [];
        if ($project) {
            $objects = $project->results()
                ->leftJoin('datision_detections', 'datision_detections.datision_result_id', '=', 'datision_results.id')
                ->select('datision_results.class', DB::raw('COUNT(DISTINCT datision_results.id) as objects_count'), DB::raw('COUNT(datision_detections.id) as detections_count'))
                ->groupBy('datision_results.class')
                ->get();

            foreach ($objects as $object) {
                $ret[$object->class] = [
                    'class' => $object->class,
                    'objects_count' => $object->objects_count,
                    'detections_count' => $object->detections_count,
                    'option' => urlencode(str_replace('/', '-----', $object->class)),
                ];
            }
            ksort($ret);
        }

        return $ret;
    }

    /**
     * Agrupa frames basándose en la distancia entre frames consecutivos
     *
     * @param  array  $frames  Array de frames (puede ser simple o con estructura asociativa)
     * @param  int  $distance  Distancia máxima permitida entre frames del mismo grupo
     * @return array Array con estructura [frame => X, group => Y]
     */
    public static function groupFramesByDistance($frames, $distance)
    {
        if (empty($frames)) {
            return [];
        }

        $result = [];
        $currentGroup = 0;
        $previousFrame = null;

        foreach ($frames as $frame) {
            // Si el frame viene en un array asociativo, extraer el valor del frame
            if (is_array($frame)) {
                $frameValue = isset($frame['frame']) ? $frame['frame'] : $frame[0];
            } else {
                $frameValue = $frame;
            }

            // Si no es el primer frame, verificar la distancia
            if ($previousFrame !== null) {
                $frameDifference = $frameValue - $previousFrame;

                // Si la distancia supera el parámetro $distance, incrementar el grupo
                if ($frameDifference > $distance) {
                    $currentGroup++;
                }
            }

            // Agregar el frame al resultado con su grupo correspondiente
            $result[] = [
                'id' => $frame['id'],
                'object_id' => $frame['object_id'],
                'class' => $frame['class'],
                'x1' => $frame['x1'],
                'y1' => $frame['y1'],
                'x2' => $frame['x2'],
                'y2' => $frame['y2'],
                'width' => $frame['width'],
                'height' => $frame['height'],
                'center_x' => $frame['center_x'],
                'center_y' => $frame['center_y'],
                'frame' => $frameValue,
                'group' => $currentGroup,
            ];

            $previousFrame = $frameValue;
        }

        return $result;
    }

    /**
     * Devuelve las detecciones de una clase concreta agrupadas por proximidad de frames.
     *
     * @param  string|int  $distance_frames
     */
    public function getObjectDetections(string $project_id, string $object_class, string $distance_frames): JsonResponse
    {
        $distance = (int) $distance_frames;                    // 1) distancia “permitida”
        $object_class = urldecode(str_replace('-----', '/', $object_class));

        $lista = IaProducts::byIaClass($object_class);

        $project = Datision::where('id_project', $project_id)->firstOrFail();

        // 2) colección ordenada por frame ASC
        $detections = DatisionDetection::whereHas('result', function ($q) use ($project, $object_class) {
            $q->where('datision_id', $project->id)
                ->where('class', $object_class);
        })
            ->with('result:id,id_object,class')
            ->orderBy('frame')
            ->get();

        // 3) asignamos grupos según distancia de frames
        $group = 0;
        $previous = null;

        $detections = $detections->map(function ($d) use ($distance, &$group, &$previous) {

            // ¿Nuevo grupo?
            if ($previous !== null && ($d->frame - $previous) > $distance) {
                $group++;
            }

            $previous = $d->frame;

            // construimos el array de salida con el campo 'group'
            return [
                'id' => $d->id,
                'object_id' => $d->result->id_object,
                'class' => $d->result->class,
                'frame' => $d->frame,
                'x1' => $d->x1,
                'y1' => $d->y1,
                'x2' => $d->x2,
                'y2' => $d->y2,
                'width' => $d->width,
                'height' => $d->height,
                'center_x' => $d->center_x,
                'center_y' => $d->center_y,
                'group' => $group,
            ];
        });

        $max = 0;
        $final = [];
        foreach ($detections->toArray() as $elemento) {
            if (! isset($final[$elemento['frame']])) {
                $final[$elemento['frame']] = [];
            }
            $final[$elemento['frame']][] = $elemento;
            if (count($final[$elemento['frame']]) > $max) {
                $max = count($final[$elemento['frame']]);
            }
        }

        $final2 = [];
        for ($n = 0; $n < $max; $n++) {
            foreach ($final as $frame => $detections) {
                if (isset($detections[$n])) {
                    $final2[] = $detections[$n];
                }
            }
        }

        $tmp = [];
        foreach ($final2 as $obj) {
            if (! isset($tmp[$obj['frame']])) {
                $tmp[$obj['frame']] = [];
            }
            $tmp[$obj['frame']][] = $obj;
        }
        $tmp2 = [];
        foreach ($tmp as $frame => $detections) {
            foreach ($detections as $obj) {
                //                if ( $obj['frame'] <= 410 ) {
                $tmp2[] = $obj;
                //                }
            }
        }

        //        $final2 = $this->groupFramesByDistance( $final2, $distance );
        //        $final2 = $this->groupFramesByDistance( $tmp2, $distance );

        if ($distance < 1) {
            $distance = 1;
        }

        $objetos = [];
        foreach ($tmp2 as $key => $obj) {
            $id = $this->buscaObjeto($objetos, $obj, $distance);
            $tmp2[$key]['group'] = $id;
        }

        $listado = '';
        foreach ($tmp2 as $obj) {
            $listado .= $obj['frame'].' '.$obj['x1'].' '.$obj['y1'].' '.$obj['group'].PHP_EOL;
        }
        //        file_put_contents( 'datos.txt', print_r( $tmp2, true ) );
        //        file_put_contents( 'datos.txt', $listado . PHP_EOL . PHP_EOL . print_r( $tmp2, true ) );

        //        return response()->json($detections);
        return response()->json(['lista' => $lista, 'detections' => $tmp2]);
    }

    public function buscaObjeto(&$objetos, $obj, $frame = 1)
    {
        $x1 = DatisionParameterController::getValue('x1');
        $y1 = DatisionParameterController::getValue('y1');

        $nuevo = array_key_last($objetos);
        if ((int) $nuevo == 0) {
            $nuevo = 0;
        }
        if (! isset($obj['veces'])) {
            $obj['veces'] = 0;
        }
        $nuevo++;
        foreach ($objetos as $key => $o) {
            if ($this->between($obj['frame'], $o['frame'] - $frame, $o['frame'] + $frame)) {
                if ($this->between($obj['x1'], $o['x1'] - $x1, $o['x1'] + $x1)) {
                    if ($this->between($obj['y1'], $o['y1'] - $y1, $o['y1'] + $y1)) {
                        $nuevo = $key;
                        $obj['veces'] = 0;
                        break;
                    }
                }
            }
            $o['veces']++;
            $objetos[$key] = $o;
        }

        $objetos[$nuevo] = $obj;

        foreach ($objetos as $key => $o) {
            if ($o['veces'] > 10) {
                unset($objetos[$key]);
            }
        }

        return $nuevo;
    }

    public function between($value, $start, $end)
    {
        return in_array($value, range($start, $end));
    }

    public function updateLinkDetections(string $project_id, string $detections_id, string $product_id): JsonResponse
    {
        echo '<h1>updateLinkDetections</h1>';
        echo '<pre>';
        echo '$project_id = '.$project_id.'<br>';
        echo '$detections_id = '.$detections_id.'<br>';
        echo '$product_id = '.$product_id.'<br>';
        echo '</pre>';

        return response()->json('');

        $request->validate([
            'project_id' => 'required|exists:datisions,id',
            'object_class' => 'required|string',
        ]);

        $project = Datision::find($request->project_id) ?? null;
        //        $project = Datision::findOrFail($request->project_id);

        $detections = [];
        if ($project) {
            $detections = DatisionDetection::whereHas('result', function ($query) use ($project, $request) {
                $query->where('datision_id', $project->id)
                    ->where('class', $request->object_class);
            })
                ->with('result:id,id_object,class')
                ->orderBy('frame')
                ->get()
                ->map(function ($detection) {
                    return [
                        'id' => $detection->id,
                        'object_id' => $detection->result->id_object,
                        'class' => $detection->result->class,
                        'frame' => $detection->frame,
                        'x1' => $detection->x1,
                        'y1' => $detection->y1,
                        'x2' => $detection->x2,
                        'y2' => $detection->y2,
                        'width' => $detection->width,
                        'height' => $detection->height,
                        'center_x' => $detection->center_x,
                        'center_y' => $detection->center_y,
                    ];
                });
        }

        return response()->json($detections);
    }

    public function exportToHotpoints(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'class' => 'required|string',
            'groups' => 'required|array|min:1',
            'groups.*.detection_ids' => 'required|array|min:1',
            'groups.*.detection_ids.*' => 'integer|exists:datision_detections,id',
        ]);

        $projectId = (int) $validated['project_id'];
        $className = $validated['class'];
        $groups = $validated['groups'];

        $project = Project::findOrFail($projectId);
        $videoInfo = ['fps' => 24, 'width' => 1920, 'height' => 1080];
        if ($project->filename) {
            $videoPath = public_path('uploads/'.$project->filename);
            if (file_exists($videoPath)) {
                $videoInfo = getVideoInfo($videoPath);
            }
        }
        $fps = $videoInfo['fps'] ?: 24;
        $videoW = $videoInfo['width'] ?: 1920;
        $videoH = $videoInfo['height'] ?: 1080;

        $iaClass = DatisionObjectsIaClass::firstOrCreate(['name' => $className]);

        $brand = Brand::firstOrCreate(
            ['name' => 'AI Generated'],
            ['disabled' => 0]
        );

        $product = Product::select('products.id')
            ->join('products_datision_objects_ia_classes as piv', 'products.id', '=', 'piv.products_id')
            ->where('piv.datision_objects_ia_classes_id', $iaClass->id)
            ->first();

        if (! $product) {
            $product = Product::create([
                'name' => '[AI] '.$className,
                'disabled' => true,
                'brands_id' => $brand->id,
                'is_ai_generated' => true,
            ]);
            DB::table('products_datision_objects_ia_classes')->insert([
                'products_id' => $product->id,
                'datision_objects_ia_classes_id' => $iaClass->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $productId = $product->id;

        $allDetectionsIds = [];
        foreach ($groups as $group) {
            foreach ($group['detection_ids'] as $detId) {
                $allDetectionsIds[] = (int) $detId;
            }
        }

        $existingImported = Hotpoint::where('versions_id', $projectId)
            ->where('products_id', $productId)
            ->where('is_ai_imported', true)
            ->whereIn('datision_detection_id', $allDetectionsIds)
            ->pluck('datision_detection_id')
            ->toArray();

        $existingSet = array_flip($existingImported);

        $exportedCount = 0;
        $skippedCount = 0;
        $precalculatedAll = [];
        $segmentsAll = [];

        foreach ($groups as $group) {
            $detections = DatisionDetection::whereIn('id', $group['detection_ids'])
                ->orderBy('frame')
                ->get();

            if ($detections->isEmpty()) {
                $skippedCount++;

                continue;
            }

            $newDetections = $detections->filter(fn ($d) => ! isset($existingSet[$d->id]));

            if ($newDetections->isEmpty()) {
                $skippedCount++;

                continue;
            }

            $frames = [];
            foreach ($newDetections as $det) {
                $centerX = ($det->x1 + $det->x2) / 2;
                $centerY = ($det->y1 + $det->y2) / 2;
                $time = $det->frame / $fps;
                $posX = $centerX / $videoW;
                $posY = $centerY / $videoH;

                Hotpoint::create([
                    'versions_id' => $projectId,
                    'products_id' => $productId,
                    'time' => $time,
                    'pos_x' => round($posX, 4),
                    'pos_y' => round($posY, 4),
                    'is_ai_imported' => true,
                    'ai_imported_at' => now(),
                    'datision_detection_id' => $det->id,
                    'status' => 'draft',
                ]);

                $frames[] = [
                    'frame' => $det->frame,
                    'center_x' => $centerX,
                    'center_y' => $centerY,
                    'video_w' => $videoW,
                    'video_h' => $videoH,
                    'fps' => $fps,
                ];
            }

            $exportedCount++;

            $interpolated = $this->interpolateDetections($frames);
            $precalculatedAll = array_merge($precalculatedAll, $interpolated);

            if (! empty($frames)) {
                $firstFrame = $frames[0];
                $lastFrame = end($frames);
                $inicio = $firstFrame['frame'] / $fps;
                $fin = $lastFrame['frame'] / $fps;

                $midIdx = (int) (count($frames) / 2);
                $targets = [];
                $targets[] = [
                    'time' => round($firstFrame['frame'] / $fps, 4),
                    'pcx' => round($firstFrame['center_x'] / $videoW, 4),
                    'pcy' => round($firstFrame['center_y'] / $videoH, 4),
                ];
                if (count($frames) > 2) {
                    $targets[] = [
                        'time' => round($frames[$midIdx]['frame'] / $fps, 4),
                        'pcx' => round($frames[$midIdx]['center_x'] / $videoW, 4),
                        'pcy' => round($frames[$midIdx]['center_y'] / $videoH, 4),
                    ];
                }
                $targets[] = [
                    'time' => round($lastFrame['frame'] / $fps, 4),
                    'pcx' => round($lastFrame['center_x'] / $videoW, 4),
                    'pcy' => round($lastFrame['center_y'] / $videoH, 4),
                ];

                $segmentsAll[] = [
                    'target' => $targets,
                    'inicio' => round($inicio, 4),
                    'fin' => round($fin, 4),
                ];
            }
        }

        if ($exportedCount > 0) {
            $this->mergeEditorJson($projectId, $productId, $product->name, $segmentsAll, $precalculatedAll);
        }

        $message = $exportedCount.' object(s) exported to hotpoints';
        if ($skippedCount > 0) {
            $message .= ', '.$skippedCount.' skipped (already imported)';
        }
        $message .= '.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'exported_count' => $exportedCount,
            'skipped_count' => $skippedCount,
            'products_created' => $exportedCount > 0 ? 1 : 0,
        ]);
    }

    private function interpolateDetections(array $frames): array
    {
        if (empty($frames)) {
            return [];
        }

        $result = [];
        $fps = $frames[0]['fps'];
        $videoW = $frames[0]['video_w'];
        $videoH = $frames[0]['video_h'];

        for ($i = 0; $i < count($frames) - 1; $i++) {
            $current = $frames[$i];
            $next = $frames[$i + 1];

            $t1 = $current['frame'] / $fps;
            $t2 = $next['frame'] / $fps;
            $pcx1 = $current['center_x'] / $videoW;
            $pcy1 = $current['center_y'] / $videoH;
            $pcx2 = $next['center_x'] / $videoW;
            $pcy2 = $next['center_y'] / $videoH;

            $result[] = [
                'time' => round($t1, 4),
                'pcx' => round($pcx1, 4),
                'pcy' => round($pcy1, 4),
            ];

            $duration = $t2 - $t1;
            $step = 0.1;
            if ($duration > $step + 0.001) {
                for ($t = $t1 + $step; $t < $t2 - 0.001; $t += $step) {
                    $ratio = ($t - $t1) / $duration;
                    $result[] = [
                        'time' => round($t, 4),
                        'pcx' => round($pcx1 + ($pcx2 - $pcx1) * $ratio, 4),
                        'pcy' => round($pcy1 + ($pcy2 - $pcy1) * $ratio, 4),
                    ];
                }
            }
        }

        if (! empty($frames)) {
            $last = end($frames);
            $lastT = $last['frame'] / $fps;
            $result[] = [
                'time' => round($lastT, 4),
                'pcx' => round($last['center_x'] / $videoW, 4),
                'pcy' => round($last['center_y'] / $videoH, 4),
            ];
        }

        return $result;
    }

    private function mergeEditorJson(int $projectId, int $productId, string $productName, array $newSegments, array $newPrecalculated): void
    {
        $existing = DB::table('datos_editor_hotpoints')
            ->where('versiones_id', $projectId)
            ->first();

        $json = [];
        if ($existing && ! empty($existing->data)) {
            $decoded = json_decode($existing->data, true);
            if (is_array($decoded)) {
                $json = $decoded;
            }
        }

        $foundKey = null;
        foreach ($json as $key => $entry) {
            if (isset($entry['producto']) && (int) $entry['producto'] === $productId) {
                $foundKey = $key;
                break;
            }
        }

        if ($foundKey !== null) {
            $json[$foundKey]['segmentos'] = array_merge(
                $json[$foundKey]['segmentos'] ?? [],
                $newSegments
            );
            $json[$foundKey]['segmentos_precalculados'] = array_merge(
                $json[$foundKey]['segmentos_precalculados'] ?? [],
                $newPrecalculated
            );
        } else {
            $maxId = 0;
            foreach ($json as $entry) {
                if (isset($entry['identificador']) && $entry['identificador'] > $maxId) {
                    $maxId = $entry['identificador'];
                }
            }

            $json[] = [
                'identificador' => $maxId + 1,
                'producto' => $productId,
                'nombre' => $productName,
                'segmentos' => $newSegments,
                'segmentos_precalculados' => $newPrecalculated,
            ];
        }

        DB::table('datos_editor_hotpoints')
            ->updateOrInsert(
                ['versiones_id' => $projectId],
                ['data' => json_encode($json)]
            );
    }

    protected function groupDetectionsByProximity(Collection $detections, int $frameGap, int $xRange, int $yRange): array
    {
        $groups = [];
        $groupIdCounter = 0;

        foreach ($detections as $det) {
            $matched = false;

            foreach ($groups as $gid => &$group) {
                $frameDiff = $det->frame - $group['last_frame'];

                if ($frameDiff >= 0 && $frameDiff <= $frameGap) {
                    if (abs($det->x1 - $group['last_x']) <= $xRange &&
                        abs($det->y1 - $group['last_y']) <= $yRange) {
                        $group['detection_ids'][] = $det->id;
                        $group['frames'][] = $det->frame;
                        $group['last_frame'] = $det->frame;
                        $group['last_x'] = $det->x1;
                        $group['last_y'] = $det->y1;
                        $matched = true;
                        break;
                    }
                }
            }
            unset($group);

            if (! $matched) {
                $groupIdCounter++;
                $groups[$groupIdCounter] = [
                    'detection_ids' => [$det->id],
                    'frames' => [$det->frame],
                    'last_frame' => $det->frame,
                    'last_x' => $det->x1,
                    'last_y' => $det->y1,
                ];
            }
        }

        return $groups;
    }

    public function autoExportToHotpoints(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'proximity_frames' => 'required|integer|min:0',
            'proximity_x' => 'required|integer|min:0',
            'proximity_y' => 'required|integer|min:0',
        ]);

        $projectId = (int) $validated['project_id'];
        $proximityFrames = (int) $validated['proximity_frames'];
        $proximityX = (int) $validated['proximity_x'];
        $proximityY = (int) $validated['proximity_y'];

        $project = Project::findOrFail($projectId);

        $videoInfo = ['fps' => 24, 'width' => 1920, 'height' => 1080];
        if ($project->filename) {
            $videoPath = public_path('uploads/'.$project->filename);
            if (file_exists($videoPath)) {
                $videoInfo = getVideoInfo($videoPath);
            }
        }
        $fps = $videoInfo['fps'] ?: 24;
        $videoW = $videoInfo['width'] ?: 1920;
        $videoH = $videoInfo['height'] ?: 1080;

        $datision = Datision::where('id_project', $projectId)->first();
        if (! $datision) {
            return response()->json([
                'success' => false,
                'message' => 'No AI detections found for this project.',
            ], 404);
        }

        $db = DB::transaction(function () use ($projectId, $datision, $fps, $videoW, $videoH, $proximityFrames, $proximityX, $proximityY) {
            $existingEditorJson = DB::table('datos_editor_hotpoints')
                ->where('versiones_id', $projectId)
                ->value('data');

            $batch = AiImportBatch::create([
                'project_id' => $projectId,
                'previous_editor_json' => $existingEditorJson,
                'created_product_ids' => [],
                'created_brand_ids' => [],
            ]);

            $results = $datision->results()
                ->with('detections')
                ->get()
                ->groupBy('class');

            $createdProductIds = [];
            $createdBrandIds = [];

            $allSegmentsByProduct = [];
            $allPrecalculatedByProduct = [];

            $totalGroups = 0;

            foreach ($results as $className => $classResults) {
                $iaClass = DatisionObjectsIaClass::firstOrCreate(['name' => $className]);

                $brand = Brand::firstOrCreate(
                    ['name' => 'AI Generated'],
                    ['disabled' => 0]
                );
                if ($brand->wasRecentlyCreated) {
                    $createdBrandIds[] = $brand->id;
                }

                $product = Product::select('products.id')
                    ->join('products_datision_objects_ia_classes as piv', 'products.id', '=', 'piv.products_id')
                    ->where('piv.datision_objects_ia_classes_id', $iaClass->id)
                    ->first();

                $productWasCreated = false;
                if (! $product) {
                    $product = Product::create([
                        'name' => '[AI] '.$className,
                        'disabled' => true,
                        'brands_id' => $brand->id,
                        'is_ai_generated' => true,
                    ]);
                    DB::table('products_datision_objects_ia_classes')->insert([
                        'products_id' => $product->id,
                        'datision_objects_ia_classes_id' => $iaClass->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $productWasCreated = true;
                    $createdProductIds[] = $product->id;
                }

                $productId = $product->id;

                $allDetections = collect();
                foreach ($classResults as $result) {
                    $alreadyImportedCount = Hotpoint::where('datision_result_id', $result->id)->count();
                    if ($alreadyImportedCount > 0) {
                        continue;
                    }

                    $resultDetections = $result->detections()->orderBy('frame')->get();
                    foreach ($resultDetections as $d) {
                        $allDetections->push((object) [
                            'id' => $d->id,
                            'frame' => $d->frame,
                            'x1' => $d->x1,
                            'y1' => $d->y1,
                            'x2' => $d->x2,
                            'y2' => $d->y2,
                            'datision_result_id' => $result->id,
                            'center_x' => ($d->x1 + $d->x2) / 2,
                            'center_y' => ($d->y1 + $d->y2) / 2,
                        ]);
                    }
                }

                if ($allDetections->isEmpty()) {
                    continue;
                }

                $allDetections = $allDetections->sortBy('frame')->values();
                $groups = $this->groupDetectionsByProximity($allDetections, $proximityFrames, $proximityX, $proximityY);

                $segmentsAll = [];
                $precalculatedAll = [];

                foreach ($groups as $groupId => $group) {
                    $detectionIds = $group['detection_ids'];
                    $detections = DatisionDetection::whereIn('id', $detectionIds)
                        ->orderBy('frame')
                        ->get();

                    if ($detections->isEmpty()) {
                        continue;
                    }

                    $frames = [];
                    foreach ($detections as $det) {
                        $centerX = ($det->x1 + $det->x2) / 2;
                        $centerY = ($det->y1 + $det->y2) / 2;
                        $time = $det->frame / $fps;
                        $posX = $centerX / $videoW;
                        $posY = $centerY / $videoH;

                        Hotpoint::create([
                            'versions_id' => $projectId,
                            'products_id' => $productId,
                            'time' => $time,
                            'pos_x' => round($posX, 4),
                            'pos_y' => round($posY, 4),
                            'is_ai_imported' => true,
                            'ai_imported_at' => now(),
                            'datision_detection_id' => $det->id,
                            'datision_result_id' => $det->datision_result_id,
                            'ai_import_batch_id' => $batch->id,
                            'status' => 'draft',
                        ]);

                        $frames[] = [
                            'frame' => $det->frame,
                            'center_x' => $centerX,
                            'center_y' => $centerY,
                            'video_w' => $videoW,
                            'video_h' => $videoH,
                            'fps' => $fps,
                        ];
                    }

                    $totalGroups++;

                    $interpolated = $this->interpolateDetections($frames);
                    $precalculatedAll = array_merge($precalculatedAll, $interpolated);

                    if (! empty($frames)) {
                        $firstFrame = $frames[0];
                        $lastFrame = end($frames);
                        $inicio = $firstFrame['frame'] / $fps;
                        $fin = $lastFrame['frame'] / $fps;

                        $midIdx = (int) (count($frames) / 2);
                        $targets = [];
                        $targets[] = [
                            'time' => round($firstFrame['frame'] / $fps, 4),
                            'pcx' => round($firstFrame['center_x'] / $videoW, 4),
                            'pcy' => round($firstFrame['center_y'] / $videoH, 4),
                        ];
                        if (count($frames) > 2) {
                            $targets[] = [
                                'time' => round($frames[$midIdx]['frame'] / $fps, 4),
                                'pcx' => round($frames[$midIdx]['center_x'] / $videoW, 4),
                                'pcy' => round($frames[$midIdx]['center_y'] / $videoH, 4),
                            ];
                        }
                        $targets[] = [
                            'time' => round($lastFrame['frame'] / $fps, 4),
                            'pcx' => round($lastFrame['center_x'] / $videoW, 4),
                            'pcy' => round($lastFrame['center_y'] / $videoH, 4),
                        ];

                        $segmentsAll[] = [
                            'target' => $targets,
                            'inicio' => round($inicio, 4),
                            'fin' => round($fin, 4),
                        ];
                    }
                }

                if (! empty($segmentsAll) || ! empty($precalculatedAll)) {
                    $allSegmentsByProduct[$productId] = $segmentsAll;
                    $allPrecalculatedByProduct[$productId] = $precalculatedAll;
                }
            }

            $batch->update([
                'created_product_ids' => $createdProductIds,
                'created_brand_ids' => $createdBrandIds,
            ]);

            foreach ($allSegmentsByProduct as $pId => $segments) {
                $pName = Product::where('id', $pId)->value('name') ?? 'Unknown';
                $this->mergeEditorJson($projectId, $pId, $pName, $segments, $allPrecalculatedByProduct[$pId] ?? []);
            }

            return $totalGroups;
        });

        $message = "Auto-transfer completed: {$db} object group(s) exported to hotpoints as draft.";

        return response()->json([
            'success' => true,
            'message' => $message,
            'exported_count' => $db,
        ]);
    }

    public function undoAutoExport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
        ]);

        $projectId = (int) $validated['project_id'];

        $batch = AiImportBatch::where('project_id', $projectId)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (! $batch) {
            return response()->json([
                'success' => false,
                'message' => 'No active auto-import batch found to undo.',
            ], 404);
        }

        DB::transaction(function () use ($batch, $projectId) {
            Hotpoint::where('ai_import_batch_id', $batch->id)->delete();

            $productIds = $batch->created_product_ids ?? [];
            if (! empty($productIds)) {
                Product::whereIn('id', $productIds)->delete();
                DB::table('products_datision_objects_ia_classes')
                    ->whereIn('products_id', $productIds)
                    ->delete();
            }

            $brandIds = $batch->created_brand_ids ?? [];
            if (! empty($brandIds)) {
                Brand::whereIn('id', $brandIds)->delete();
            }

            if ($batch->previous_editor_json) {
                DB::table('datos_editor_hotpoints')
                    ->updateOrInsert(
                        ['versiones_id' => $projectId],
                        ['data' => $batch->previous_editor_json]
                    );
            } else {
                DB::table('datos_editor_hotpoints')
                    ->where('versiones_id', $projectId)
                    ->delete();
            }

            $batch->update(['status' => 'undone']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Auto-import undone successfully. Hotpoints, products, and brands have been reverted.',
        ]);
    }
}
