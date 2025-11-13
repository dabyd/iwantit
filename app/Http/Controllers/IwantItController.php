<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use App\Models\Hotpoint;
use Illuminate\Support\Facades\URL;
use App\Models\License;
use App\Http\Controllers\ProductController;

class IwantitController extends Controller {
    /**
    * @hideFromAPIDocumentation
    */
    public function api_iwi_post( Request $request ) {
        $this->api_iwi_get( $request );
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
     *
     * @response 401 {
     *   "error": "The key does not exist or is incorrect"
     * }
     *
     * @response 403 {
     *   "error": "The key has been disabled, please contact the administrator"
     * }
     *
     * @response 403 scenario="Key not valid for project" {
     *   "error": "This key is not valid for this project, please contact the administrator"
     * }
     *
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

    public function api_iwi_get( Request $request ) {
        switch( $request->action ) {
            case 'load':
                echo $this->load_hotpoints( $request->id );
                die();
                break;

            case 'save':
                echo $this->save_hotpoints( $request->id, $request->data );
                die();
                break;

            case 'get':
                $this->get_hotpoints( $request );
                break;

            case 'get_projects':
                $this->get_projects( $request );
                break;

            case 'create_keyfile':
                $this->create_keyfile( $request );
                die();
                break;

            case 'enable_disable_keyfile':
                $this->enable_disable_keyfile( $request );
                die();
                break;

            case 'delete_keyfile':
                $this->delete_keyfile( $request );
                die();
                break;

            case 'download_keyfile':
                $this->download_keyfile( $request );
                die();
                break;

            case 'update_keyfile_name':
                $this->update_keyfile_name( $request );
                die();
                break;

            default:
                echo '<h1>default</h1>';
                $this->not_allowed( $request );
                break;
        }
    }

    static public function load_hotpoints( $id ) {
        $data = DB::table( 'datos_editor_hotpoints' )
                    ->where( 'versiones_id', '=', $id )
                    ->get();
        $ret = 'bad';
        if ( count( $data ) > 0 ) {
            $ret = json_decode( $data->toArray()[ 0 ]->data );
        }
        return json_encode( $ret );
    }

    static public function save_hotpoints( $id, $data ) {
        $tmp = DB::table( 'datos_editor_hotpoints' )
                ->where( 'versiones_id', '=', $id )
                ->get()
                ->toArray();
        if ( count( $tmp ) > 0 ) {
            $tmp = DB::table( 'datos_editor_hotpoints' )
                        ->where( 'versiones_id', '=', $id )
                        ->update( [ 'data' => $data ] );
        } else {
            DB::insert('insert into datos_editor_hotpoints (versiones_id, data) values (?, ?)', [ $id, $data ]);
        }

        $data = json_decode( $data );

        $productos = [];
        foreach( $data as $producto ) {
            if ( !isset( $productos[ $producto->producto ] ) ) {
                $productos[ $producto->producto ] = '*';
                DB::table( 'hotpoints' )
                    ->where( [
                        [ 'versions_id', '=', $id ],
                        [ 'products_id', '=', $producto->producto ]
                    ] )
                    ->delete();
            }
            foreach( $producto->segmentos_precalculados as $segmento ) {
                $hotpoint = new Hotpoint();
                $hotpoint->versions_id = $id;
                $hotpoint->products_id = $producto->producto;
                $hotpoint->time = $segmento->time;
                $hotpoint->pos_x = $segmento->pcx;
                $hotpoint->pos_y = $segmento->pcy;
                $hotpoint->save();
            }
        }
        echo json_encode("OK");
//                else echo json_encode("BAD");
        die();



        /*
    $result = mysqli_query($conn, "replace into datos_editor_hotpoints (nombre, data) values ('".addslashes(trim($nombre))."', '".addslashes(trim($data))."')");
    if($result) {
//            $result = mysqli_query($conn, "select @@identity");       // Devuelve el último id introducido.
        echo json_encode("OK");
    }
    else echo json_encode("BAD");
*/
    }

    public static function getQueryWithBindings($query): string {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            $binding = addslashes($binding);
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }

    public function create_keyfile( $request ) {
        $license = new License();
        $license->versions_id = $request->id;
        $license->disabled = '0';
        $license->key = $this->keyGenerator();
        $license->save();
    }

    public function enable_disable_keyfile( $request ) {
        $license = License::find($request->id);
        if ($license) {
            if ( '0' == $license->disabled ) {
                $license->disabled = '1';
            } else {
                $license->disabled = '0';
            }
            $license->save();
        }
    }

    public function delete_keyfile( $request ) {
        $license = License::find($request->id);
        if ($license) {
            $license->delete();
        }
    }

    public function download_keyfile( $request ) {
        $license = License::find($request->id);
        if ($license) {
            $downloadFileName = 'iwantit.xml';
            header('Content-Description: File Transfer');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename='.$downloadFileName);
            ob_clean();
            flush();
            echo $license->key;
        }
    }

    public function update_keyfile_name( $request ) {
        $license = License::find($request->id);
        if ($license) {
            $license->name = $request->name;
            $license->save();
            $kf = ProjectController::generateFileKey( $license->versions_id );
            $fn = '';
            foreach( $kf as $f ) {
                if ( $f->id == $request->id ) {
                    $fn = $f->fn;
                }
            }
            echo json_encode( [ 'id' => $request->id, 'fn' => $fn ] );
        }
        die();
    }

    public function get_projects( $request ) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        $prjs = DB::table( 'projects' )
            ->get()
            ->toArray();
        $obj = [];
        foreach( $prjs as $pr ) {
            $movie = [
                'id' => $pr->id,
                'name' => $pr->name,
                'url' => '/uploads/' . $pr->filename,
            ];
            $obj[] = $movie;
        }
        echo json_encode( $obj );
        die();
    }

    public function get_hotpoints( $request ) {
        $debug = false;
        //
        // Comrpruebo que la licencia pasada es correcta
        //
        $license = DB::table( 'licenses' )
            ->where( [
                [ 'versions_id', '=', $request->vid ],
                [ 'key', '=', $request->key ],
            ])
            ->get()
            ->toArray();
        $tmp = [
            'id' => 25,
            'name' => 'Emilys_demo1',
            'versions_id' => '12',
            'disabled' => 0,
            'key' => 'xxxx'
        ];

        switch ( $request->key ) {
            case 'validKeyForTestingOnly':
                $license = [ (object) $tmp ];
                break;
            
            case 'disabledKeyForTestingOnly':
                $license = [ (object) $tmp ];
                $license[0]->disabled = 1;
                break;

             case 'invalidKeyForTestingOnly':
                $license = [];
                break;
                
            case 'invalidKeyForProjectForTestingOnly':
                echo json_encode( [ 'error' => 'This key is not valid for this project, please contact with the administrator' ] );
                die();
                break;

            case 'expiredProjectKeyForTestingOnly':
                echo json_encode( [ 'error' => 'The key has expired, please contact with the administrator' ] );
                die();
                break;
        }

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        if ( count( $license ) > 0 ) {
            //
            // La licencia existe
            //
            if ( '0' == $license[ 0 ]->disabled ) {
                //
                // La licencia, a parte de existir, está habilitada
                //
                //
                // Recojo todos los PRODUCTOS, repetidos o no, que estén en esa versión y tiempo inidcado
                //
                $datos = DB::table( 'hotpoints' )
                    ->where([
                        [ 'versions_id', '=', $request->vid ],
                        [ DB::raw('ROUND( time, 4 )'), DB::raw( ' ROUND ( "' . $request->time . '", 4 )' ) ],
                    ]);
                if ( $debug ) {
                    echo '<pre>';
                    echo '<h1>Sentencia SQL</h1>';
                    echo '<h3>' . $this->getQueryWithBindings( $datos ) . '</h3>';
                }
                $datos = $datos->get()->toArray();

                if ( $debug ) {
                    echo '<h1>Datos en crudo</h1>';
                    print_r( $datos );
                    echo '<hr>';
                }
                //
                // Elimino de la selección los PRODUCTOS desactivados
                //
                if ( $debug ) {
                    echo '<h1>Elimino productos desactivados</h1>';
                }
                $tmp = DB::table( 'products' )
                    ->where( 'disabled', '0' )
                    ->get()
                    ->toArray();
                $data = [];
                foreach( $tmp as $dato ) {
                    $data[ $dato->id ] = $dato;
                }
                foreach( $datos as $k => $pr ) {
                    if ( !isset( $data[ $pr->products_id ] ) ) {
                        if ( $debug ) {
                            echo 'Se elimina el producto ID: ' . $pr->products_id . '<br>';
                        }
                        unset( $datos[ $k ] );
                    }
                }
                if ( $debug ) {
                    print_r( $datos );
                    echo '<hr>';
                }

                //
                // Elimino de la selección las MARCAS desactivadas que están vinculados a los PRODUCTOS
                //
                if ( $debug ) {
                    echo '<h1>Elimino marcas desactivados</h1>';
                }
                $tmp = DB::table( 'brands' )
                    ->select( 'products.*' )
                    ->leftJoin( 'products', 'brands.id', '=', 'products.brands_id' )
                    ->where( 'brands.disabled', '0' )
                    ->get()
                    ->toArray();
                $data = [];
                foreach( $tmp as $dato ) {
                    $data[ $dato->id ] = $dato;
                }
                foreach( $datos as $k => $pr ) {
                    if ( !isset( $data[ $pr->products_id ] ) ) {
                        if ( $debug ) {
                            echo 'Se elimina el producto ID: ' . $pr->products_id . ' porque pertenece a una marca desabilitada<br>';
                        }
                        unset( $datos[ $k ] );
                    }
                }
                if ( $debug ) {
                    print_r( $datos );
                    echo '<hr>';
                }

                //
                // Elimino de la selección los TAGS desactivados que están vinculados a los PRODUCTOS
                //
                if ( $debug ) {
                    echo '<h1>Elimino tags desactivados</h1>';
                }
                $tmp = DB::table( 'products_tags' )
                    ->select( 'products.*' )
                    ->leftJoin( 'products', 'products_tags.products_id', '=', 'products.id' )
                    ->leftJoin( 'tags', 'products_tags.tags_id', '=', 'tags.id' )
                    ->where( 'tags.disabled', '1' )
                    ->get()
                    ->toArray();
                $data = [];
                foreach( $tmp as $dato ) {
                    $data[ $dato->id ] = $dato;
                }
                foreach( $datos as $k => $pr ) {
                    if ( isset( $data[ $pr->products_id ] ) ) {
                        if ( $debug ) {
                            echo 'Se elimina el producto ID: ' . $pr->products_id . ' porque está vinculado a un tag desabilitado<br>';
                        }
                        unset( $datos[ $k ] );
                    }
                }
                if ( $debug ) {
                    print_r( $datos );
                    echo '<hr>';
                }

                //
                // Elimino de la selección los PRODUCTOS, vinculados a un TAG, que ha sido desactivado el TAG para ese PRODUCTO
                //
                if ( $debug ) {
                    echo '<h1>Elimino tags vinculados a productos desactivados</h1>';
                }
                $tmp = DB::table( 'products_tags' )
                    ->select( 'products.*' )
                    ->leftJoin( 'products', 'products_tags.products_id', '=', 'products.id' )
                    ->where( 'products_tags.disabled', '1' )
                    ->get()
                    ->toArray();
                $data = [];
                foreach( $tmp as $dato ) {
                    $data[ $dato->id ] = $dato;
                }
                foreach( $datos as $k => $pr ) {
                    if ( isset( $data[ $pr->products_id ] ) ) {
                        if ( $debug ) {
                            echo 'Se elimina el producto ID: ' . $pr->products_id . ' porque está vinculado a un tag que ha sido desabilitado para este producto<br>';
                        }
                        unset( $datos[ $k ] );
                    }
                }
                if ( $debug ) {
                    print_r( $datos );
                    echo '<hr>';
                }

                //
                // Elimino de la selección los PRODUCTOS, vinculados a un TERRITORIO que tenga un TAG que ha sido desactivado para ese TERRITORIO
                //
                if ( $debug ) {
                    echo '<h1>Elimino tags desactivados para un territorio</h1>';
                }
                $tmp = DB::table( 'products_tags' )
                    ->select( 'products.*' )
                    ->leftJoin( 'products', 'products_tags.products_id', '=', 'products.id' )
                    ->leftJoin( 'territories_tags', 'products_tags.tags_id', '=', 'territories_tags.tags_id' )
                    ->where( 'territories_tags.territories_id', $request->tid )
                    ->get()
                    ->toArray();
                $data = [];
                foreach( $tmp as $dato ) {
                    $data[ $dato->id ] = $dato;
                }
                foreach( $datos as $k => $pr ) {
                    if ( isset( $data[ $pr->products_id ] ) ) {
                        if ( $debug ) {
                            echo 'Se elimina el producto ID: ' . $pr->products_id . ' porque está vinculado a un tag que ha sido desabilitado para este territorio<br>';
                        }
                        unset( $datos[ $k ] );
                    }
                }
                if ( $debug ) {
                    print_r( $datos );
                    echo '<hr>';
                }

                //
                // Preparo todo para enviarlo via JSON
                //
                $ret = null;
                foreach( $datos as $data ) {
                    if ( is_null( $ret ) ) {
                        $ret = new stdClass;
                        $ret->objetos = [];
                    }
                    $pr = DB::table( 'products' )->where( 'id', $data->products_id )->first();
                    $br = DB::table( 'brands' )->where( 'id', $pr->brands_id )->first();
                    $dato = new stdClass;
                    $dato->id = $data->products_id;
                    $dato->pos_x = $data->pos_x;
                    $dato->pos_y = $data->pos_y;
                    $dato->nombre = $pr->name;
                    $dato->descripcion = $pr->description;
                    $dato->imagen = URL::asset( 'uploads/' . $pr->filename );
                    $dato->url =  $pr->url;
                    $dato->auto_open = $pr->auto_open;
                    $dato->marca = $br->name;
                    $dato->logo = URL::asset( 'uploads/' . $br->filename );
                    $dato->url_marca =  $br->url;
                    if ( '' != $pr->icono ) {
                        $dato->hotpoint_icon = URL::asset( 'uploads/' . $pr->icono );
                    }
                    $ret->objetos[] = $dato;
                }
                if ( $debug ) {
                    echo '<h1>Lo que se envía (sin formato json)</h1>';
                    print_r( $ret );
                } else {
                    echo json_encode( $ret );
                }
            } else {
                // Licencia desabilitada
               echo json_encode( [ 'error' => 'The key has been disabled, please contact with the administrator' ] );
            }
        } else {
            // Licencia no válida o inexistente
            echo json_encode( [ 'error' => 'The key does not exist or is incorrect'] );
        }
        die();
    }


    public function not_allowed( Request $request ) {
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
        die();
    }

    public function keyGenerator( $length = 1024 ) {
        // Generar una secuencia de bytes aleatoria
        $randomBytes = openssl_random_pseudo_bytes($length);
        // Convertir la secuencia de bytes en una cadena hexadecimal
        $sshKey = bin2hex($randomBytes);
        // Imprimir la cadena generada
        return $sshKey;
    }

}
