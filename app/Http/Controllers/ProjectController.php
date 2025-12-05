<?php

namespace App\Http\Controllers;

use App\Models\Project;
// use App\Models\ProjectsUsers;
use App\Models\DatisionObjectsIaClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\IwantItController;
use App\Http\Controllers\DatisionController;
use App\Models\Brand;
use App\Models\Hotpoint;
use App\Models\Product;
use App\Models\ProductDatisionObjectsIaClass;
use App\Models\HotpointsDate;

class ProjectController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index() {
        // Construimos la query base y paginamos
        $projects = $this->getProjects()->distinct()->paginate(300);
        $controller = $this;

        // --- OPTIMIZADO: consultar estados de IA en paralelo ---
        try {
            $machineUrl = DB::table('datision_parameters')->value('machine_url');

            // Paso 1: Recoger todos los task_ids y resetear el campo
            $tasksToCheck = [];
            foreach ($projects->items() as $project) {
                $taskId = $project->ai_task_id;
                $project->ai_task_id = '--'; // Valor por defecto
                
                if ($taskId !== null && $taskId !== '') {
                    $tasksToCheck[$project->id] = $taskId;
                }
            }

            // Paso 2: Hacer todas las peticiones HTTP en paralelo
            if (!empty($tasksToCheck) && !empty($machineUrl)) {
                $baseUrl = rtrim($machineUrl, '/') . ':5018/v1/get_result/';
                
                $responses = Http::pool(fn ($pool) => 
                    collect($tasksToCheck)->map(fn ($taskId, $projectId) =>
                        $pool->as((string)$projectId)
                            ->timeout(5)
                            ->connectTimeout(2)
                            ->acceptJson()
                            ->get($baseUrl . $taskId)
                    )->toArray()
                );

                // Paso 3: Procesar las respuestas
                foreach ($projects->items() as $project) {
                    $projectId = (string)$project->id;
                    
                    if (!isset($responses[$projectId])) {
                        continue;
                    }
                    
                    $resp = $responses[$projectId];
                    
                    if ($resp instanceof \Throwable || $resp->failed()) {
                        continue;
                    }

                    $json = $resp->json();
                    if (!$json && $resp->body()) {
                        $maybe = json_decode($resp->body(), true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $json = $maybe;
                        }
                    }

                    if (!$json || !isset($json['state'])) {
                        continue;
                    }

                    switch ($json['state']) {
                        case 'PROGRESS':
                            $percent = isset($json['status']) ? trim((string)$json['status']) : '';
                            $project->ai_task_id = $percent !== '' ? "In Progress: {$percent}" : "In Progress";
                            break;
                        case 'PENDING':
                            $project->ai_task_id = "Pending...";
                            break;
                        case 'SUCCESS':
                            $project->ai_task_id = '--';
                            break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Si falla, no rompemos el listado
            \Log::warning('AI bulk check error: ' . $e->getMessage());
        }
        // --- FIN OPTIMIZADO ---

        // Territorios (tal cual tenías)
        $territorios = DB::table('territories')->get();
        $terr = [];
        foreach ($territorios->toArray() as $territory) {
            $terr[$territory->id] = ['id' => $territory->id, 'name' => $territory->name];
        }

        return view('projects.index', compact('projects', 'controller', 'terr'))
            ->with('i', (request()->input('page', 1) - 1) * 300);
    }

    /*
    public function index() {
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
    */

    public function getUsersByProject($projectId = null): array {
        $query = Project::query();
    
        // Filtramos si se pasa un ID o un array de IDs
        if ($projectId) {
            if (is_array($projectId)) {
                $query->whereIn('id', $projectId);
            } else {
                $query->where('id', $projectId);
            }
        }
    
        $projects = $query->get();
        $result = [];
    
        foreach ($projects as $project) {
            $users = [];
    
            // Owner directo
            if ($project->users_id) {
                $ownerUser = DB::table('users')
                    ->select('id as user_id', 'name as user_name', 'role as user_role')
                    ->where('id', $project->users_id)
                    ->first();
    
                if ($ownerUser) {
                    $users[] = [
                        'user_id' => $ownerUser->user_id,
                        'user_name' => $ownerUser->user_name,
                        'user_role' => $ownerUser->user_role,
                        'owner' => 'Project owner'
                    ];
                }
            }
    
            // Users vinculados desde projects_users
            $linkedUsers = DB::table('projects_users')
                ->join('users', 'projects_users.users_id', '=', 'users.id')
                ->where('projects_users.projects_id', $project->id)
                ->select(
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.role as user_role',
                    'projects_users.as_owner'
                )
                ->get();
    
            foreach ($linkedUsers as $lu) {
                $ownerStatus = ($lu->as_owner === 'S') ? 'Shared owner' : 'Editor';
                $alreadyAdded = collect($users)->contains('user_id', $lu->user_id);
                if (!$alreadyAdded) {
                    $users[] = [
                        'user_id' => $lu->user_id,
                        'user_name' => $lu->user_name,
                        'user_role' => $lu->user_role,
                        'owner' => $ownerStatus
                    ];
                }
            }
    
            $result[$project->id] = $users;
        }
    
        return $result;
    }

    // GET: /projects/{id}/available-users
    public function getAvailableUsers($id) {
        $project = Project::findOrFail($id);

        $userIdsInProject = DB::table('projects_users')
            ->where('projects_id', $id)
            ->pluck('users_id')
            ->toArray();

        if ($project->users_id) {
            $userIdsInProject[] = $project->users_id;
        }

        $users = DB::table('users')
            ->whereNotIn('id', $userIdsInProject)
            ->where('role', '!=', 'admin')
            ->select('id', 'name', 'role')
            ->get();

        return response()->json($users);
    }

    // POST: /projects/{id}/add-user
    public function addUserToProject(Request $request, $id) {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|in:NO,shared_owner'
        ]);

        $exists = DB::table('projects_users')
            ->where('projects_id', $id)
            ->where('users_id', $request->user_id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'User already linked']);
        }

        DB::table('projects_users')->insert([
            'projects_id' => $id,
            'users_id' => $request->user_id,
            'as_owner' => $request->role === 'shared_owner' ? 'S' : null,
        ]);

        return response()->json(['success' => true]);
    }

    // POST: /projects/{id}/remove-user
    public function removeUserFromProject(Request $request, $id) {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID.',
                'errors' => $e->errors()
            ], 422);
        }
    
        $project = Project::findOrFail($id);
    
        if ($project->users_id == $validated['user_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the main project owner.'
            ], 403);
        }
    
        DB::table('projects_users')
            ->where('projects_id', $id)
            ->where('users_id', $validated['user_id'])
            ->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'User removed from project.'
        ]);
    }

    // POST: /projects/{project}/update-role
    public function updateUserRole(Request $request, $projectId) {        
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|in:shared_owner,NO',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID.',
                'errors' => $e->errors()
            ], 422);
        }

        $userId = $validated['user_id'];
        $role = $validated['role'] === 'shared_owner' ? 'S' : 'N';

        DB::table('projects_users')
            ->where('projects_id', $projectId)
            ->where('users_id', $userId)
            ->update(['as_owner' => $role]);

        return response()->json([
            'success' => true,
            'message' => 'User role updated',
        ]);
    }

    public function getProjects($projectId = null) {
        $user = auth()->user();
    
        // Base para cada subconsulta
        $ownProjects = Project::select('projects.*', DB::raw("'SI' as owner"))
            ->where('users_id', $user->id);
    
        $sharedAsOwner = Project::select('projects.*', DB::raw("'SI' as owner"))
            ->join('projects_users', 'projects.id', '=', 'projects_users.projects_id')
            ->where('projects_users.users_id', $user->id)
            ->where('projects_users.as_owner', 'S');
    
        $sharedNormal = Project::select('projects.*', DB::raw("'NO' as owner"))
            ->join('projects_users', 'projects.id', '=', 'projects_users.projects_id')
            ->where('projects_users.users_id', $user->id)
            ->where(function ($query) use ($user) {
                $query->where('projects.users_id', '!=', $user->id)
                    ->orWhereNull('projects.users_id');
            })
            ->where(function ($query) {
                $query->whereNull('projects_users.as_owner')
                    ->orWhere('projects_users.as_owner', '!=', 'S');
            });
    
        // Si hay projectId, filtramos
        if ($projectId) {
            if (is_array($projectId)) {
                $ownProjects->whereIn('projects.id', $projectId);
                $sharedAsOwner->whereIn('projects.id', $projectId);
                $sharedNormal->whereIn('projects.id', $projectId);
            } else {
                $ownProjects->where('projects.id', $projectId);
                $sharedAsOwner->where('projects.id', $projectId);
                $sharedNormal->where('projects.id', $projectId);
            }
        }
    
        if ($user->role === 'admin') {
            $query = Project::select('projects.*', DB::raw("'NO' as owner"));
            if ($projectId) {
                $query->when(is_array($projectId), fn($q) => $q->whereIn('id', $projectId))
                      ->when(!is_array($projectId), fn($q) => $q->where('id', $projectId));
            }
            return $query;
        }
    
        if ($user->role === 'editor') {
            return $sharedAsOwner->union($sharedNormal);
        }
    
        return $ownProjects
            ->union($sharedAsOwner)
            ->union($sharedNormal);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $controller = $this;

        //
        // Territorios
        //
        $territorios = DB::table('territories')->get();
        $terr = [];
        foreach( $territorios->toArray() as $territorio ) {
            $terr[ $territorio->id ] = [ 'id' => $territorio->id, 'name' => $territorio->name ];
        }
        return view('projects.create', compact('controller','terr'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse {
        $max_size = (int) ini_get('upload_max_filesize') * 1024 * 1024;
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:Film,Serie',
            'season' => 'nullable|integer',
            'episode' => 'nullable|integer',
        ]);

        $file = $request->file('filename');
        $prj = $request->all();
        if ( !is_null( $file ) ) {
            $file_name = time() . '.' . $file->extension();
            $file->move( public_path('uploads'), $file_name );
            $prj[ 'original_filename' ] = $prj[ 'filename' ];
            $prj[ 'filename' ] = $file_name;
        }
        $prj['users_id'] = auth()->user()->id;
        unset($prj['_token']);
        Project::create($prj);
        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function inform($id) {
        $controller = $this;
        $obj = DB::table( 'hotpoints' )
            ->select( [
                DB::raw( 'hotpoints.products_id AS pr_id' ),
                DB::raw( 'products.filename AS pr_image' ),
                DB::raw( 'products.name AS pr_name' ),
                DB::raw( 'brands.id AS br_id' ),
                DB::raw( 'brands.filename AS br_logo' ),
                DB::raw( 'brands.name AS br_name' ),
                DB::raw( 'COUNT(*) AS veces' )
            ])
            ->where( 'versions_id', '=', $id )
            ->leftJoin( 'products', 'products.id', '=', 'hotpoints.products_id' )
            ->leftJoin( 'brands', 'products.brands_id', '=', 'brands.id' )
            ->groupBy( 'products_id')
            ->orderBy( 'veces', 'DESC' );
        $obj2 = $obj
            ->get()
            ->toArray();

//             SELECT * FROM `demo-i-want-it`.hotpoints where versions_id = 5 and products_id = 2;

/*
        echo '<pre style="margin-left: 300px">';
        print_r( str_replace( '?', $id, $obj->toSql() ) );
        echo '<hr>';
        print_r( $obj2 );
//        print_r( $obj );
        echo '</pre>';
*/
        foreach( $obj2 as $key => $pr ) {
            if ( '0' != $pr->pr_id ) {
                $obj = DB::table( 'hotpoints' )
                    ->where( 'versions_id', '=', $id )
                    ->where( 'products_id', '=', $pr->pr_id );
                $obj3 = $obj
                    ->get()
                    ->toArray();
                $tiempos = [];
                foreach( $obj3 as $e ) {
                    $tiempos[ round( $e->time, 0 ) ] = '*';
                }
                $obj2[ $key ]->veces = ProjectController::seconds2time( count( $tiempos ) );
            }
        }

        return view('projects.inform', compact('obj2', 'controller'));
    }

    public function seconds2time( $seconds ) {
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);
        $timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        return $timeFormat;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project) {
        $controller = $this;
        echo 'inform';
        die();
        return view('projects.show', compact('project', 'controller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Project $project) {
        $url = $request->url();
        if ( isset( $_GET[ 'add' ] ) ) {
            DB::table('versions_tags')->insert(
                ['versions_id' => $project->id, 'tags_id' => $_GET[ 'add' ]]
            );
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'remove' ] ) ) {
            DB::table('versions_tags')
                ->where( [
                    [ 'id', '=', $_GET[ 'remove' ] ]
                ] )
                ->delete();
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'change_status' ] ) ) {
            $status = ( $_GET[ 'status' ] == '0' ? '1' : '0' );
            DB::table('versions_tags')
                ->where( [
                    [ 'id', '=', $_GET[ 'change_status' ] ]
                ] )
                ->update( [ 'disabled' => $status ] );
            header('Location: ' . $url);
        }

        $video = URL::asset( 'uploads/' . $project->filename );
        $video_path = public_path( 'uploads/' . $project->filename );
        $video_fps = getVideoFPS( $video_path );
        $video_h = getVideoResolution( $video_path );
        $video_w = $video_h[ 'width' ];
        $video_h = $video_h[ 'height' ]; 

        //
        // Tags vinculados y disponibles
        //
        $controller = $this;
        $all_tags = DB::table('tags')->get();
        $vinculated_tags = DB::table('versions_tags')
            ->select( 'versions_tags.*', 'tags.name as name')
            ->leftJoin('tags', 'versions_tags.tags_id', '=', 'tags.id')
            ->where('versions_tags.versions_id', $project->id )
            ->get();
        $tags = [];
        $vinculated = [];
        foreach( $all_tags->toArray() as $tag ) {
            $tags[ $tag->id ] = $tag;
        }
        foreach( $vinculated_tags->toArray() as $tag ) {
            $vinculated[ $tag->tags_id ] = $tag;
            if ( isset( $tags[ $tag->tags_id ] ) ) {
                unset( $tags[ $tag->tags_id ] );
            }
        }

        //
        // Productos
        //
        $products = DB::table('products')->get();
        $productos = [];
        foreach( $products->toArray() as $producto ) {
            $productos[ $producto->id ] = [ 'id' => $producto->id, 'name' => $producto->name ];
        }

        //
        // Hotpoints
        //
        $hotpoints = IwantitController::load_hotpoints( $project->id );

        //
        // Territorios
        //
        $territories = DB::table('territories')->get();
        $terr = [];
        foreach( $territories->toArray() as $territory ) {
            $terr[ $territory->id ] = [ 'id' => $territory->id, 'name' => $territory->name ];
        }
/*
        //
        // Proyectos a los que tiene acceso
        //
        $gp = $this->getProjects( $project->id )->get()->toArray();
*/
        //
        // Usuarios por proyecto
        //
        $ubp = $this->getUsersByProject( $project->id );

        //
        // Licencias que nos son licencias sino que son keyfile
        //
        $kf = self::generateFileKey( $project->id );

        //
        // Detecciones de datision por proyecto
        //
        $datision = DatisionController::getProjectObjects( $project->id );
/*
        echo '<pre>*';
        print_r( $datision );
        echo '</pre>';
        die();
*/
        $distance_frames = 0;

        // --- NUEVO: obtener parámetros de datision_parameters ---
        $datisionParams = DB::table('datision_parameters')->first();
        $ai_url = $datisionParams->machine_url ?? null;
        $threshold_secs = $datisionParams->threshold_sec ?? null;
        // Carga ordenada alfabéticamente por 'name' de todas las clases de objetos para la IA
        $ia_clases = DatisionObjectsIaClass::query()
            ->orderBy('name', 'asc')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // --------------------------------------------------------

        $tmp = Hotpoint::getGroupedHotpoints( $project->id );
        $objects = [];
        foreach( $tmp->toArray() as $data ) {
            $key = $data['products_id'];
            if ( !isset( $objects[ $key ] ) ) {
                $objects[ $key ] = [];
            }
            $objects[ $key ] = $data['time_groups']->toArray();
        }
        foreach( $objects as $object_id => $veces ) {
            $ttime = 0;
            foreach( $veces as $key => $data ) {
                $last = end( $data );
                $first = reset( $data );
                $time = $last['time'] - $first['time'];
                $ttime += $time;
                $veces[ $key ] = [
                    'time' => $time,
                    'veces' => $data
                ];
            }
            $prd = Product::find($object_id);

            // Si el producto no existe, saltamos esta iteración
            if (!$prd) {
                unset($objects[$object_id]);
                continue;
            }

            $cls = ProductDatisionObjectsIaClass::where('products_id', $object_id)->get();
            $brd = Brand::find($prd->brands_id);
            $hpd = HotpointsDate::where('project_id', $project->id)
                        ->where('product_id', $object_id)
                        ->first();
            $estado = 'Enabled';
            $precio = '';
            $precio_s = '';
            $date_in = '';
            $date_out = '';
            $url = '';
            if ( $hpd ) {
                $estado = $hpd->getEstadoTextAttribute();
                $precio = $hpd->getPriceFormattedAttribute( ceil( $ttime ), 0 );
                $precio_s = $hpd->getPriceRawRounded();
                $date_in = $hpd->get_date_in();
                $date_out = $hpd->get_date_out();
                $url = $hpd->url;
            }
            $clsname = '';
            foreach( $cls as $c ) {
                $clx = DatisionObjectsIaClass::find( $c->datision_objects_ia_classes_id );
                if ( '' != $clsname ) {
                    $clsname .= ', ';
                }
                $clsname .= $clx->name;
            }
            $objects[ $object_id ] = [
                'id' => $object_id,
                'thumbnail' => '/uploads/' . $prd->filename,
                'thumbnail_brand' => '/uploads/' . $brd->filename,
                'name' => $prd->name,
                'family' => $clsname,
                'brand' => $brd->name,
                'time' => formatSecondsToTime( $ttime ),
                'segundos' => ceil( $ttime ),
                'estado' => $estado,
                'precio' => $precio == '' ? 'No price' : $precio,
                'precio_s' => $precio_s == '' ? '0' : $precio_s,
                'date_in' => $date_in == '' ? '---' : $date_in,
                'date_out' => $date_out == '' ? '---' : $date_out,
                'url' => $url,
                'url_brand' => $brd->url,
                'veces' => count( $veces ),
                'data' => $veces,
            ];
        }

        return view('projects.edit', compact( 'project', 'controller', 'video', 'video_path', 'video_fps', 'video_w', 'video_h', 'hotpoints', 'productos', 'terr', 'kf', 'ubp', 'datision', 'distance_frames', 'ai_url', 'threshold_secs', 'ia_clases', 'objects' ));
    }

    static public function generateFileKey( $project_id ) {
        $kf = DB::table('licenses')
            ->where('versions_id', $project_id )
            ->get();
        $base = public_path() .'/keyfile/';
        foreach( $kf as $k => $file ) {
            // $fn = md5( $file->key ) . '-iwik.xml';
            $fn = self::cleanFileName( $file->name );
            $kf[ $k ]->fn = $fn;
            file_put_contents( $base . $fn, $file->key );
        }
        return $kf;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
/*
    public function update(Request $request, Project $project) {
        $request->validate([
            'name' => 'required',
        ]);
        $prj = $request->all();

        if ( isset( $prj[ 'filename' ] ) ) {
            // Sube nuevo vídeo
            unlink( public_path('uploads') . '/' . $prj[ 'old_video' ] );
            $file = $request->file('filename');
            $file_name = time() . '.' . $file->extension();
            $file->move( public_path('uploads'), $file_name );
            $prj[ 'original_filename' ] = $prj[ 'filename' ];
            $prj[ 'filename' ] = $file_name;
        }
        unset( $prj['_token'] );
        $project->update($prj);
        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }
*/

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required',
            // otros campos...
        ]);

        // Copia editable de los datos, sin el _token
        $prj = $request->except('_token');

        // ¿Subieron vídeo nuevo?
        if ($request->hasFile('filename') && $request->file('filename')->isValid()) {

            // 1) Borrar el antiguo con seguridad
            //    (esperamos que te llegue un input hidden 'old_video' con el nombre del archivo antiguo)
            if (!empty($prj['old_video'])) {
                $oldPath = public_path('uploads/' . $prj['old_video']);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                } else {
                    // opcional: log para saber por qué no existe
                    \Log::warning('Old video not found for unlink', ['path' => $oldPath]);
                }
            }

            // 2) Guardar el nuevo
            $file = $request->file('filename');
            $ext  = $file->getClientOriginalExtension(); // o $file->extension()
            $fileName = time() . '.' . strtolower($ext);

            // mueve a public/uploads
            $file->move(public_path('uploads'), $fileName);

            // 3) Actualizar campos
            $prj['original_filename'] = $prj['filename'] ?? $project->filename; // si quieres conservar el nombre previo
            $prj['filename'] = $fileName;
        }

        // Aplicar cambios al modelo
        $project->update($prj);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project) {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'projects';
        $params[ 'singular' ] = 'Project';
        $params[ 'plural' ] = 'projects';
        $params[ 'fields' ] = [
            [
                'label' => 'ID',
                'name' => 'id',
                'editable' => false,
                'orderby' => true
            ],
            [
                'label' => 'Name',
                'name' => 'name',
                'editable' => true,
                'type' => 'text',
                'orderby' => true,
                'nbsp' => true
            ],
            [
                'label' => 'Territories',
                'name' => 'territories_id',
                'editable' => true,
                'type' => 'select',
                'format' => 'related'
            ],
            [
                'label' => 'Type',
                'name' => 'type',
                'editable' => true,
                'type' => 'select',
                'format' => 'related',
                'values' => [ 'Film', 'Serie' ]
            ],
            [
                'label' => 'Season',
                'name' => 'season',
                'editable' => true,
                'type' => 'text',
                'show_when' => [
                    'field' => 'type',
                    'value' => 'Serie'
                ]
            ],
            [
                'label' => 'Episode',
                'name' => 'episode',
                'editable' => true,
                'type' => 'text',
                'show_when' => [
                    'field' => 'type',
                    'value' => 'Serie'
                ]
            ],
            [
                'label' => 'State',
                'name' => 'ai_task_id',
                'editable' => false,
                'orderby' => false
            ],
            [
                'label' => 'Filename',
                'name' => 'filename',
                'editable' => true,
                'type' => 'file',
                'hide_on_index' => true
            ]
        ];
        $ret = $params;
        if ( '' != $data && isset( $params[ $data ] ) ) {
            $ret = $params[ $data ];
        }
        return $ret;
    }

    public function getText( $id = '' ) {
        $text = [
            'left_column' => 'Available tags',
            'left_column_button' => 'Add tag to project',
            'right_column' => 'Tag related to this project',
            'right_column_button' => 'Remove tag from project',
        ];
        if  ( '' != $id ) {
            $text = $text[ $id ];
        }
        return $text;
    }

    static public function cleanFileName($file_name){
        $file_name .=  '-iwik.xml';
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_name_str = pathinfo($file_name, PATHINFO_FILENAME);

        // Replaces all spaces with hyphens.
        $file_name_str = str_replace(' ', '-', $file_name_str);
        // Removes special chars.
        $file_name_str = preg_replace('/[^A-Za-z0-9\-\_]/', '', $file_name_str);
        // Replaces multiple hyphens with single one.
        $file_name_str = preg_replace('/-+/', '-', $file_name_str);

        $clean_file_name = $file_name_str.'.'.$file_ext;

        return strtolower( $clean_file_name );
    }
}
