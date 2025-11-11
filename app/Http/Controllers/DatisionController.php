<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Datision;
use App\Models\DatisionResult;
use App\Models\DatisionDetection;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\IaProducts;
use PHPUnit\Event\Runtime\PHP;

class DatisionController extends Controller {
    /**
     * Display the main Datision index view.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View {
        $projects = $this->getProjects();

        // Paginamos al final
        $projects = $projects->distinct()->paginate(300);
        $controller = $this;

        $territorios = DB::table('territories')->get();
        $terr = [];
        foreach( $territorios->toArray() as $territory ) {
            $terr[ $territory->id ] = [ 'id' => $territory->id, 'name' => $territory->name ];
        }
        return view('projects.index', compact('projects', 'controller', 'terr'))->with('i', (request()->input('page', 1) - 1) * 300);
    }

    /**
     * Handle the incoming Datision upgrade request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upgrade(Request $request): JsonResponse {
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
        if (!$datision) {
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
            'summary' => $counters
        ]);
    }

    /**
     * Provide a CSRF token for external services.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCsrfToken(Request $request): JsonResponse {
        return response()->json(['csrf_token' => csrf_token()]);
    }

    /**
     * Get all projects with their relationships.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProjects(): JsonResponse {
        // Método original del segundo controlador
        $projects = Project::with(['projects_users', 'users'])->get();
        
        return response()->json([
            'status' => 'ok',
            'projects' => $projects
        ]);
    }

    /**
     * Get objects for a specific project.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    static public function getProjectObjects( $project_id ) {
        $project = Datision::where('id_project', $project_id)->first() ?? null;
//        $project = Datision::findOrFail( $project_id );
        $ret = [];
        if ( $project ) {
            $objects = $project->results()
                ->select('class', DB::raw('COUNT(*) as objects_count'), DB::raw('SUM((SELECT COUNT(*) FROM datision_detections WHERE datision_detections.datision_result_id = datision_results.id)) as detections_count'))
                ->groupBy('class')
                ->get()
                ->map(function ($object) {
                    return [
                        'class' => $object->class,
                        'objects_count' => $object->objects_count,
                        'detections_count' => $object->detections_count,
                    ];
                });

            $objects = $objects->toArray();
            $ret = array();
            foreach( $objects as $object ) {
                $object['option'] = urlencode( str_replace( '/', '-----', $object['class'] ) );
                $ret[ $object['class'] ] = $object;
            }
            ksort( $ret );
        }
        return $ret;
    }

    /**
    * Agrupa frames basándose en la distancia entre frames consecutivos
    * 
    * @param array $frames Array de frames (puede ser simple o con estructura asociativa)
    * @param int $distance Distancia máxima permitida entre frames del mismo grupo
    * @return array Array con estructura [frame => X, group => Y]
    */
    static public function groupFramesByDistance($frames, $distance) {
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
                'id'         => $frame['id'],
                'object_id'  => $frame['object_id'],
                'class'      => $frame['class'],
                'x1'         => $frame['x1'],
                'y1'         => $frame['y1'],
                'x2'         => $frame['x2'],
                'y2'         => $frame['y2'],
                'width'      => $frame['width'],
                'height'     => $frame['height'],
                'center_x'   => $frame['center_x'],
                'center_y'   => $frame['center_y'],
                'frame' => $frameValue,
                'group' => $currentGroup
            ];
            
            $previousFrame = $frameValue;
        }
        
        return $result;
    }

    /**
     * Devuelve las detecciones de una clase concreta agrupadas por proximidad de frames.
     *
     * @param string $project_id
     * @param string $object_class
     * @param string|int $distance_frames
     * @return \Illuminate\Http\JsonResponse
     */
    public function getObjectDetections(string $project_id, string $object_class, string $distance_frames): JsonResponse {
        $distance = (int) $distance_frames;                    // 1) distancia “permitida”
        $object_class = urldecode(str_replace('-----', '/', $object_class));

        $lista = IaProducts::byIaClass( $object_class );

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
        $group      = 0;
        $previous   = null;

        $detections = $detections->map(function ($d) use ($distance, &$group, &$previous) {

            // ¿Nuevo grupo?
            if ($previous !== null && ($d->frame - $previous) > $distance) {
                $group++;
            }

            $previous = $d->frame;

            // construimos el array de salida con el campo 'group'
            return [
                'id'         => $d->id,
                'object_id'  => $d->result->id_object,
                'class'      => $d->result->class,
                'frame'      => $d->frame,
                'x1'         => $d->x1,
                'y1'         => $d->y1,
                'x2'         => $d->x2,
                'y2'         => $d->y2,
                'width'      => $d->width,
                'height'     => $d->height,
                'center_x'   => $d->center_x,
                'center_y'   => $d->center_y,
                'group'      => $group,
            ];
        });

        $max = 0;
        $final = array();
        foreach( $detections->toArray() as $elemento ) {
            if ( ! isset( $final[ $elemento['frame'] ] ) ) {
                $final[ $elemento['frame'] ] = array();
            }
            $final[ $elemento['frame'] ][] = $elemento;
            if ( count( $final[ $elemento['frame'] ] ) > $max ) {
                $max = count( $final[ $elemento['frame'] ] );
            }
        }

        $final2 = array();
        for ( $n = 0; $n < $max; $n++ ) {
            foreach ($final as $frame => $detections) {
                if ( isset( $detections[ $n ] ) ) {
                    $final2[] = $detections[ $n ];
                }
            }
        }

        $tmp = [];
        foreach( $final2 as $obj ) {
            if ( ! isset( $tmp[ $obj['frame'] ] ) ) {
                 $tmp[ $obj['frame'] ] = [];
            }
             $tmp[ $obj['frame'] ][] = $obj;
        }
        $tmp2 = [];
        foreach($tmp as $frame => $detections) {
            foreach( $detections as $obj ) {
//                if ( $obj['frame'] <= 410 ) {
                    $tmp2[] = $obj;
//                }
            }
        }


//        $final2 = $this->groupFramesByDistance( $final2, $distance );
//        $final2 = $this->groupFramesByDistance( $tmp2, $distance );

        if ( $distance < 1 ) {
            $distance = 1;
        }

        $objetos = [];
        foreach( $tmp2 as $key => $obj ) {
            $id = $this->buscaObjeto( $objetos, $obj, $distance );
            $tmp2[$key]['group'] = $id;
        }

        $listado = '';
        foreach( $tmp2 as $obj ) {
            $listado .= $obj['frame'] . ' ' . $obj['x1'] . ' ' . $obj['y1'] . ' ' . $obj['group'] . PHP_EOL;
        }
//        file_put_contents( 'datos.txt', print_r( $tmp2, true ) );
//        file_put_contents( 'datos.txt', $listado . PHP_EOL . PHP_EOL . print_r( $tmp2, true ) );

//        return response()->json($detections);
        return response()->json( array( 'lista' => $lista, 'detections' => $tmp2 ) );
    }

    public function buscaObjeto( &$objetos, $obj, $frame = 1 ) {
        $x1 = \App\Http\Controllers\DatisionParameterController::getValue('x1');
        $y1 = \App\Http\Controllers\DatisionParameterController::getValue('y1');

        $nuevo = array_key_last( $objetos );
        if ( 0 == (int) $nuevo ) {
            $nuevo = 0;
        }
        if ( ! isset( $obj['veces'] ) ) {
            $obj['veces'] = 0;
        }
        $nuevo++;
        foreach( $objetos as $key => $o ) {            
            if ( $this->between( $obj['frame'], $o['frame'] - $frame, $o['frame'] + $frame ) ) {
                if ( $this->between( $obj['x1'], $o['x1'] - $x1, $o['x1'] + $x1 ) ) {
                    if ( $this->between( $obj['y1'], $o['y1'] - $y1, $o['y1'] + $y1 ) ) {
                        $nuevo = $key;
                        $obj['veces'] = 0;
                        break;
                    }
                }
            }
            $o['veces']++;
            $objetos[ $key ] = $o;
        }

        $objetos[ $nuevo ] = $obj;

        foreach( $objetos as $key => $o ) {
            if ( $o['veces'] > 10 ) {
                unset( $objetos[ $key ] );
            }
        }

        return $nuevo;
    }

    public function between( $value, $start, $end ) {
        return in_array( $value, range( $start, $end ) );
    }

    public function updateLinkDetections (string $project_id, string $detections_id, string $product_id): JsonResponse {
        echo '<h1>updateLinkDetections</h1>';
        echo '<pre>';
        echo '$project_id = ' . $project_id . '<br>';
        echo '$detections_id = ' . $detections_id . '<br>';
        echo '$product_id = ' . $product_id . '<br>';
        echo '</pre>';

        return response()->json('');
        
        $request->validate([
            'project_id' => 'required|exists:datisions,id',
            'object_class' => 'required|string'
        ]);

        $project = Datision::find($request->project_id) ?? null;
//        $project = Datision::findOrFail($request->project_id);
        
        $detections = [];
        if ( $project ) {
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

}