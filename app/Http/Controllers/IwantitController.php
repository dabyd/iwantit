<?php

namespace App\Http\Controllers;

use App\Models\ClickStatistic;
use App\Models\Hotpoint;
use App\Models\License;
use App\Models\Project;
use App\Models\Territory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use stdClass;

class IwantitController extends Controller
{
    /**
     * @hideFromAPIDocumentation
     */
    public function api_iwi_post(Request $request)
    {
        $this->api_iwi_get($request);
    }

    /**
     * Retrieves hotpoints (products) for a video at a specific timestamp.
     *
     * This endpoint returns a list of products detected in a video (`vid`) at a specific moment (`time`),
     * as long as a valid and active API key (`key`) for the requested version is provided.
     *
     * There are different possible responses depending on the key and parameters provided:
     * - If the [translate:key] is valid and active for the project, the list of products is returned.
     * - If the [translate:key] is disabled for the project, a specific error message is returned.
     * - If the [translate:key] is invalid or not active for the project, another error message is returned.
     * - If the [translate:key] has expired, a different error message is also returned.
     *
     * __Example parameters__:
     * - [translate:action]: get
     * - [translate:vid]: 12
     * - [translate:time]: 142.2
     * - [translate:key]: validKeyForTestingOnly, disabledKeyForTestingOnly, invalidKeyForTestingOnly invalidKeyForProjectForTestingOnly, expiredProjectKeyForTestingOnly, or real key
     *
     * @queryParam action string required The action for get the data. Example: get
     * @queryParam vid integer required The ID of the video to query. Example: 12
     * @queryParam time float required The timestamp (in seconds) to query. Example: 142.2
     * @queryParam key string required The authentication key (SHA-512). Example: validKeyForTestingOnly
     *
     * @response 200 {
     *   "objects": [
     *     {
     *       "id": 1,
     *       "pos_x": 0.5,
     *       "pos_y": 0.5,
     *       "name": "Smartphone XYZ",
     *       "description": "A state-of-the-art smartphone.",
     *       "image": "http://uat.i-want-it.es/uploads/product_image.jpg",
     *       "url": "http://example.com/product/1",
     *       "auto_open": 0,
     *       "brand": "TechBrand",
     *       "logo": "http://uat.i-want-it.es/uploads/brand_logo.png",
     *       "brand_url": "http://example.com/brand",
     *       "hotpoint_icon": "http://uat.i-want-it.es/uploads/hotpoint_icon.png"
     *     }
     *   ]
     * }
     * @response 401 {
     *   "error": "The key does not exist or is incorrect"
     * }
     * @response 403 {
     *   "error": "The key has been disabled, please contact the administrator"
     * }
     * @response 403 scenario="Key not valid for project" {
     *   "error": "This key is not valid for this project, please contact the administrator"
     * }
     * @response 403 scenario="Key expired" {
     *   "error": "The key has expired, please contact the administrator"
     * }
     *
     * __Accepted test keys:__
     * - "validKeyForTestingOnly": Valid and active key (200 OK response)
     * - "disabledKeyForTestingOnly": Key disabled for this project (403 error)
     * - "invalidKeyForTestingOnly": Incorrect key for this project (401 error)
     * - "invalidKeyForProjectForTestingOnly": Key not valid for the requested project (403 error)
     * - "expiredProjectKeyForTestingOnly": Key has expired (403 error)
     */
    public function api_iwi_get(Request $request)
    {
        switch ($request->action) {
            case 'load':
                echo $this->load_hotpoints($request->id);
                exit();
                break;

            case 'save':
                echo $this->save_hotpoints($request->id, $request->data);
                exit();
                break;

            case 'get':
                $this->get_hotpoints($request);
                break;

            case 'get_projects':
                $this->get_projects($request);
                break;

            case 'create_keyfile':
                $this->create_keyfile($request);
                exit();
                break;

            case 'enable_disable_keyfile':
                $this->enable_disable_keyfile($request);
                exit();
                break;

            case 'delete_keyfile':
                $this->delete_keyfile($request);
                exit();
                break;

            case 'download_keyfile':
                $this->download_keyfile($request);
                exit();
                break;

            case 'download_plain_keyfile':
                $this->download_plain_keyfile($request);
                exit();
                break;

            case 'update_keyfile_name':
                $this->update_keyfile_name($request);
                exit();
                break;

            default:
                echo '<h1>default</h1>';
                $this->not_allowed($request);
                break;
        }
    }

    public static function load_hotpoints($id)
    {
        $data = DB::table('datos_editor_hotpoints')
            ->where('versiones_id', '=', $id)
            ->get();
        $ret = 'bad';
        if (count($data) > 0) {
            $ret = json_decode($data->toArray()[0]->data);
        }

        return json_encode($ret);
    }

    public static function save_hotpoints($id, $data)
    {
        $tmp = DB::table('datos_editor_hotpoints')
            ->where('versiones_id', '=', $id)
            ->get()
            ->toArray();
        if (count($tmp) > 0) {
            $tmp = DB::table('datos_editor_hotpoints')
                ->where('versiones_id', '=', $id)
                ->update(['data' => $data]);
        } else {
            DB::insert('insert into datos_editor_hotpoints (versiones_id, data) values (?, ?)', [$id, $data]);
        }

        $data = json_decode($data);

        $productos = [];
        foreach ($data as $producto) {
            if (! isset($productos[$producto->producto])) {
                $productos[$producto->producto] = '*';
                DB::table('hotpoints')
                    ->where([
                        ['versions_id', '=', $id],
                        ['products_id', '=', $producto->producto],
                    ])
                    ->delete();
            }
            foreach ($producto->segmentos_precalculados as $segmento) {
                $hotpoint = new Hotpoint;
                $hotpoint->versions_id = $id;
                $hotpoint->products_id = $producto->producto;
                $hotpoint->time = $segmento->time;
                $hotpoint->pos_x = $segmento->pcx;
                $hotpoint->pos_y = $segmento->pcy;
                $hotpoint->save();
            }
        }
        echo json_encode('OK');
        //                else echo json_encode("BAD");
        exit();

        /*
    $result = mysqli_query($conn, "replace into datos_editor_hotpoints (nombre, data) values ('".addslashes(trim($nombre))."', '".addslashes(trim($data))."')");
    if($result) {
//            $result = mysqli_query($conn, "select @@identity");       // Devuelve el último id introducido.
        echo json_encode("OK");
    }
    else echo json_encode("BAD");
*/
    }

    public static function getQueryWithBindings($query): string
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            $binding = addslashes($binding);

            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

    public function create_keyfile($request)
    {
        $license = new License;
        $license->versions_id = $request->id;
        $license->disabled = '0';
        $license->key = $this->keyGenerator();
        $license->save();
    }

    public function enable_disable_keyfile($request)
    {
        $license = License::find($request->id);
        if ($license) {
            if ($license->disabled == '0') {
                $license->disabled = '1';
            } else {
                $license->disabled = '0';
            }
            $license->save();
        }
    }

    public function delete_keyfile($request)
    {
        $license = License::find($request->id);
        if ($license) {
            $license->delete();
        }
    }

    public function download_keyfile($request)
    {
        $license = License::find($request->id);
        if ($license) {
            $project = Project::find($license->versions_id);
            $duration = 0.0;
            if ($project && $project->filename) {
                $videoPath = public_path('uploads/'.$project->filename);
                $duration = getVideoDuration($videoPath);
            }
            $keyContent = $license->versions_id."\r\n".$license->key;
            if ($duration > 0) {
                $fileContent = generateSrtContent($duration, $keyContent);
            } else {
                $fileContent = "1\r\n00:00:00,000 --> 00:00:00,000\r\n".$keyContent."\r\n\r\n";
            }

            $downloadFileName = $project ? $this->buildDownloadFilename($project, 'srt', $license->name ?? '') : 'iwantit.srt';
            header('Content-Description: File Transfer');
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename='.$downloadFileName);
            ob_clean();
            flush();
            echo $fileContent;
        }
    }

    public function download_plain_keyfile($request)
    {
        $license = License::find($request->id);
        if ($license) {
            $project = Project::find($license->versions_id);
            $downloadFileName = $project ? $this->buildDownloadFilename($project, 'key', $license->name ?? '') : 'iwantit.key';
            header('Content-Description: File Transfer');
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename='.$downloadFileName);
            ob_clean();
            flush();
            echo $license->key;
        }
    }

    private function slugify(string $str): string
    {
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        $str = strtolower($str);
        $str = preg_replace('/[^a-z0-9]+/', '_', $str);

        return trim($str, '_');
    }

    private function buildDownloadFilename(Project $project, string $extension, string $keyName = ''): string
    {
        $base = $this->slugify($project->name);
        if ($project->type === 'Serie') {
            $territory = Territory::find($project->territories_id);
            if ($territory) {
                $base .= '_'.$this->slugify($territory->name);
            }
            $base .= '_s'.(int) $project->season.'_e'.(int) $project->episode;
        }
        if ($keyName !== '') {
            $sluggedKey = $this->slugify($keyName);
            if ($sluggedKey !== '') {
                $base .= '_'.$sluggedKey;
            }
        }

        return $base.'.'.$extension;
    }

    public function update_keyfile_name($request)
    {
        $license = License::find($request->id);
        if ($license) {
            $license->name = $request->name;
            $license->save();
            $kf = ProjectController::generateFileKey($license->versions_id);
            $fn = '';
            foreach ($kf as $f) {
                if ($f->id == $request->id) {
                    $fn = $f->fn;
                }
            }
            echo json_encode(['id' => $request->id, 'fn' => $fn]);
        }
        exit();
    }

    public function get_projects($request)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        $prjs = DB::table('projects')
            ->get()
            ->toArray();
        $obj = [];
        foreach ($prjs as $pr) {
            $movie = [
                'id' => $pr->id,
                'name' => $pr->name,
                'url' => 'uploads/'.$pr->filename,
            ];
            $obj[] = $movie;
        }
        echo json_encode($obj);
        exit();
    }

    public function get_hotpoints($request)
    {
        $debug = false;
        //
        // Comrpruebo que la licencia pasada es correcta
        //
        $license = DB::table('licenses')
            ->where([
                ['versions_id', '=', $request->vid],
                ['key', '=', $request->key],
            ])
            ->get()
            ->toArray();
        $tmp = [
            'id' => 25,
            'name' => 'Emilys_demo1',
            'versions_id' => '12',
            'disabled' => 0,
            'key' => 'xxxx',
        ];

        switch ($request->key) {
            case 'validKeyForTestingOnly':
                $license = [(object) $tmp];
                break;

            case 'disabledKeyForTestingOnly':
                $license = [(object) $tmp];
                $license[0]->disabled = 1;
                break;

            case 'invalidKeyForTestingOnly':
                $license = [];
                break;

            case 'invalidKeyForProjectForTestingOnly':
                echo json_encode(['error' => 'This key is not valid for this project, please contact with the administrator']);
                exit();
                break;

            case 'expiredProjectKeyForTestingOnly':
                echo json_encode(['error' => 'The key has expired, please contact with the administrator']);
                exit();
                break;
        }

        if ($request->vid == '25') {
            $license = [(object) $tmp];
        }

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        if (count($license) > 0) {
            //
            // La licencia existe
            //
            if ($license[0]->disabled == '0') {
                //
                // La licencia, a parte de existir, está habilitada
                //
                // Registrar la visualización en estadísticas
                try {
                    Log::info('Triggering logView for request', ['vid' => $request->vid, 'key' => $request->key]);
                    ClickStatistic::logView($request);
                } catch (\Exception $e) {
                    // Log error but don't fail the request
                    \Log::error('Error logging view statistic: '.$e->getMessage(), [
                        'exception' => $e,
                        'request' => $request->all(),
                    ]);
                }

                // Optimized query to fetch hotpoints with product and brand data,
                // while applying all necessary filters (disabled products, brands, tags, etc.)
                $query = DB::table('hotpoints')
                    ->select(
                        'hotpoints.products_id',
                        'hotpoints.pos_x',
                        'hotpoints.pos_y',
                        'products.name as product_name',
                        'products.description as product_description',
                        'products.filename as product_filename',
                        'products.url as product_url',
                        'products.auto_open as product_auto_open',
                        'products.icono as product_icon',
                        'brands.id as brand_id',
                        'brands.name as brand_name',
                        'brands.filename as brand_logo',
                        'brands.url as brand_url'
                    )
                    ->join('products', 'hotpoints.products_id', '=', 'products.id')
                    ->leftJoin('brands', 'products.brands_id', '=', 'brands.id')
                    // Filter: Product and Brand global disabled status
                    // products.disabled = '1' means disabled
                    ->where('products.disabled', '0')
                    ->where(function ($q) {
                        $q->whereNull('brands.id') // Allow products without brands (if desired, otherwise use join)
                            ->orWhere('brands.disabled', '0');
                    })
                    // Filter: Per-project product status (hotpoints_dates)
                    // We exclude if a record exists with estado = '0' (Disabled)
                    ->whereNotExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
                            ->from('hotpoints_dates')
                            ->whereRaw('hotpoints_dates.product_id = hotpoints.products_id')
                            ->where('hotpoints_dates.project_id', $request->vid)
                            ->where('hotpoints_dates.estado', '0');
                    })
                    // Filter: Tags (exclude products if any of their tags are disabled)
                    ->whereNotExists(function ($q) {
                        $q->select(DB::raw(1))
                            ->from('products_tags')
                            ->join('tags', 'products_tags.tags_id', '=', 'tags.id')
                            ->whereRaw('products_tags.products_id = hotpoints.products_id')
                            ->where('tags.disabled', '1');
                    })
                    // Filter: Disabled product-tag links
                    ->whereNotExists(function ($q) {
                        $q->select(DB::raw(1))
                            ->from('products_tags')
                            ->whereRaw('products_tags.products_id = hotpoints.products_id')
                            ->where('products_tags.disabled', '1');
                    })
                    // Filter: Territory tags (exclusion list)
                    ->when($request->tid, function ($q, $tid) {
                        $q->whereNotExists(function ($sq) use ($tid) {
                            $sq->select(DB::raw(1))
                                ->from('products_tags')
                                ->join('territories_tags', 'products_tags.tags_id', '=', 'territories_tags.tags_id')
                                ->whereRaw('products_tags.products_id = hotpoints.products_id')
                                ->where('territories_tags.territories_id', $tid);
                        });
                    })
                    ->where('hotpoints.versions_id', $request->vid)
                    ->whereRaw('ROUND(hotpoints.time, 4) = ROUND(?, 4)', [$request->time]);

                if ($debug) {
                    echo '<pre><h1>Sentencia SQL Optimizada</h1><h3>'.$this->getQueryWithBindings($query).'</h3></pre>';
                }

                $datos = $query->get();

                if ($debug) {
                    echo '<h1>Datos recuperados</h1><pre>';
                    print_r($datos->toArray());
                    echo '</pre><hr>';
                }

                $ret = null;
                if ($datos->isNotEmpty()) {
                    $ret = new stdClass;
                    $ret->objetos = [];
                    foreach ($datos as $item) {
                        $dato = new stdClass;
                        $dato->id = $item->products_id;
                        $dato->pos_x = $item->pos_x;
                        $dato->pos_y = $item->pos_y;
                        $dato->nombre = $item->product_name;
                        $dato->descripcion = $item->product_description;
                        $dato->imagen = route('track.image', ['id' => $item->products_id]).'?vid='.$request->vid.'&time='.$request->time;
                        // URLs envueltas con endpoint de tracking
                        $dato->url = route('track.click', ['type' => 'product', 'id' => $item->products_id]).'?vid='.$request->vid.'&time='.$request->time;
                        $dato->url_original = $item->product_url;
                        $dato->auto_open = $item->product_auto_open;
                        $dato->marca = $item->brand_name;
                        $dato->logo = $item->brand_logo ? URL::asset('uploads/'.$item->brand_logo) : null;
                        $dato->url_marca = $item->brand_id ? route('track.click', ['type' => 'brand', 'id' => $item->brand_id]).'?vid='.$request->vid : null;
                        $dato->url_marca_original = $item->brand_url;
                        if (! empty($item->product_icon)) {
                            $dato->hotpoint_icon = URL::asset('uploads/'.$item->product_icon);
                        }
                        $ret->objetos[] = $dato;
                    }
                }

                if ($debug) {
                    echo '<h1>Lo que se envía (sin formato json)</h1>';
                    echo '<pre>';
                    print_r($ret);
                    echo '</pre>';
                } else {
                    echo json_encode($ret);
                }
            } else {
                // Licencia desabilitada
                echo json_encode(['error' => 'The key has been disabled, please contact with the administrator']);
            }
        } else {
            // Licencia no válida o inexistente
            echo json_encode(['error' => 'The key does not exist or is incorrect']);
        }
        exit();
    }

    public function not_allowed(Request $request)
    {
        /*
                echo '<pre>';
                echo '<h1>_REQUEST</h1>';
                print_r( $_REQUEST );
                echo '<h1>_GET</h1>';
                print_r( $_GET );
                echo '<h1>_POST</h1>';
                print_r( $_POST );
        */
        echo '<h1>I Want It. API Rest Interface. Service not available</h1>';
        exit();
    }

    public function keyGenerator($length = 20)
    {
        // Generar una secuencia de bytes aleatoria (20 bytes = 40 caracteres hexadecimales)
        $randomBytes = openssl_random_pseudo_bytes($length);
        // Convertir la secuencia de bytes en una cadena hexadecimal
        $sshKey = bin2hex($randomBytes);

        return $sshKey;
    }
}
