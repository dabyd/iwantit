<?php

namespace App\Http\Controllers;

use App\Helpers\ProjectPermissionHelper;
// use App\Models\ProjectsUsers;
use App\Models\DatisionObjectsIaClass;
use App\Models\Hotpoint;
use App\Models\HotpointsDate;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $search = request()->get('search', '');
        $perPage = (int) request()->get('per_page', 10);
        $allowedPerPages = [10, 20, 50, 100, 9999];
        if (! in_array($perPage, $allowedPerPages)) {
            $perPage = 10;
        }

        $projects = $this->getProjects()->distinct();

        if (strlen($search) >= 3) {
            $projects = $projects->where('projects.name', 'like', '%'.$search.'%');
        }

        if ($perPage === 9999) {
            $projects = $projects->get();
            $projects = new LengthAwarePaginator($projects, $projects->count(), $projects->count());
        } else {
            $projects = $projects->paginate($perPage);
        }

        $controller = $this;

        $user = auth()->user();
        $isAdminOrSuper = $user->hasRole('Admin') || $user->hasRole('Supervisor');

        $userProjectPermissions = DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->pluck('access_level', 'project_id')
            ->toArray();

        $canDeleteProjects = [];
        if ($isAdminOrSuper) {
            foreach ($projects->items() as $p) {
                $canDeleteProjects[$p->id] = true;
            }
        } else {
            foreach ($projects->items() as $p) {
                $isOwner = $p->users_id == $user->id;
                $hasCreatePermission = isset($userProjectPermissions[$p->id]) && $userProjectPermissions[$p->id] === 'create';
                if ($isOwner || $hasCreatePermission) {
                    $canDeleteProjects[$p->id] = true;
                }
            }
        }

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
            if (! empty($tasksToCheck) && ! empty($machineUrl)) {
                $baseUrl = rtrim($machineUrl, '/').':5018/v1/get_result/';

                $responses = Http::pool(fn ($pool) => collect($tasksToCheck)->map(fn ($taskId, $projectId) => $pool->as((string) $projectId)
                    ->timeout(2)
                    ->connectTimeout(1)
                    ->acceptJson()
                    ->get($baseUrl.$taskId)
                )->toArray()
                );

                // Paso 3: Procesar las respuestas
                foreach ($projects->items() as $project) {
                    $projectId = (string) $project->id;

                    if (! isset($responses[$projectId])) {
                        continue;
                    }

                    $resp = $responses[$projectId];

                    if ($resp instanceof \Throwable || $resp->failed()) {
                        continue;
                    }

                    $json = $resp->json();
                    if (! $json && $resp->body()) {
                        $maybe = json_decode($resp->body(), true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $json = $maybe;
                        }
                    }

                    if (! $json || ! isset($json['state'])) {
                        continue;
                    }

                    switch ($json['state']) {
                        case 'PROGRESS':
                            $percent = isset($json['status']) ? trim((string) $json['status']) : '';
                            $project->ai_task_id = $percent !== '' ? "In Progress: {$percent}" : 'In Progress';
                            break;
                        case 'PENDING':
                            $project->ai_task_id = 'Pending...';
                            break;
                        case 'SUCCESS':
                            $project->ai_task_id = '--';
                            break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Si falla, no rompemos el listado
            \Log::warning('AI bulk check error: '.$e->getMessage());
        }
        // --- FIN OPTIMIZADO ---

        // Territorios (tal cual tenías)
        $territorios = DB::table('territories')->get();
        $terr = [];
        foreach ($territorios->toArray() as $territory) {
            $terr[$territory->id] = ['id' => $territory->id, 'name' => $territory->name];
        }

        return view('projects.index', compact('projects', 'controller', 'terr', 'canDeleteProjects'))
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

    public function getUsersByProject($projectId = null): array
    {
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
                        'owner' => 'Project owner',
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
                if (! $alreadyAdded) {
                    $users[] = [
                        'user_id' => $lu->user_id,
                        'user_name' => $lu->user_name,
                        'user_role' => $lu->user_role,
                        'owner' => $ownerStatus,
                    ];
                }
            }

            $result[$project->id] = $users;
        }

        return $result;
    }

    /**
     * Versión optimizada que recibe el proyecto ya cargado (evita query extra).
     */
    public function getUsersByProjectDirect(Project $project): array
    {
        $users = [];

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
                    'owner' => 'Project owner',
                ];
            }
        }

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
            $accessLevel = DB::table('project_user_permissions')
                ->where('user_id', $lu->user_id)
                ->where('project_id', $project->id)
                ->value('access_level') ?? 'read';

            $ownerStatus = ($lu->as_owner === 'S') ? 'Shared owner' : 'Editor';
            $alreadyAdded = collect($users)->contains('user_id', $lu->user_id);
            if (! $alreadyAdded) {
                $users[] = [
                    'user_id' => $lu->user_id,
                    'user_name' => $lu->user_name,
                    'user_role' => $lu->user_role,
                    'owner' => $ownerStatus,
                    'access_level' => $accessLevel,
                ];
            }
        }

        return [$project->id => $users];
    }

    // GET: /projects/{id}/available-users
    public function getAvailableUsers($id)
    {
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
    public function addUserToProject(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|in:NO,shared_owner',
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
    public function removeUserFromProject(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID.',
                'errors' => $e->errors(),
            ], 422);
        }

        $project = Project::findOrFail($id);

        if ($project->users_id == $validated['user_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove the main project owner.',
            ], 403);
        }

        DB::table('projects_users')
            ->where('projects_id', $id)
            ->where('users_id', $validated['user_id'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'User removed from project.',
        ]);
    }

    // POST: /projects/{project}/update-role
    public function updateUserRole(Request $request, $projectId)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|in:shared_owner,NO',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID.',
                'errors' => $e->errors(),
            ], 422);
        }

        $userId = $validated['user_id'];
        $isSharedOwner = $validated['role'] === 'shared_owner';

        DB::table('projects_users')
            ->where('projects_id', $projectId)
            ->where('users_id', $userId)
            ->update(['as_owner' => $isSharedOwner ? 'S' : null]);

        $accessLevel = $isSharedOwner ? 'write' : 'read';
        DB::table('project_user_permissions')
            ->updateOrInsert(
                ['user_id' => $userId, 'project_id' => $projectId],
                ['access_level' => $accessLevel]
            );

        return response()->json([
            'success' => true,
            'message' => 'User role updated',
        ]);
    }

    public function updateAccessLevel(Request $request, $projectId)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_level' => 'required|in:read,write,create',
        ]);

        DB::table('project_user_permissions')
            ->updateOrInsert(
                ['user_id' => $validated['user_id'], 'project_id' => $projectId],
                ['access_level' => $validated['access_level']]
            );

        return response()->json(['success' => true]);
    }

    public function getProjects($projectId = null)
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            $query = Project::select('projects.*', 'users.name as owner_name')
                ->leftJoin('users', 'projects.users_id', '=', 'users.id');
            if ($projectId) {
                $query->when(is_array($projectId), fn ($q) => $q->whereIn('projects.id', $projectId))
                    ->when(! is_array($projectId), fn ($q) => $q->where('projects.id', $projectId));
            }

            return $query;
        }

        $permittedProjectIds = DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->where('access_level', '!=', 'none')
            ->pluck('project_id');

        $ownProjects = Project::select('projects.*', 'users.name as owner_name')
            ->leftJoin('users', 'projects.users_id', '=', 'users.id')
            ->where('projects.users_id', $user->id);

        if ($projectId) {
            if (is_array($projectId)) {
                $permittedProjectIds = $permittedProjectIds->filter(fn ($id) => in_array($id, $projectId));
            } else {
                $permittedProjectIds = $permittedProjectIds->filter(fn ($id) => $id == $projectId);
            }
        }

        $sharedAsOwner = Project::select('projects.*', 'users.name as owner_name')
            ->leftJoin('users', 'projects.users_id', '=', 'users.id')
            ->join('projects_users', 'projects.id', '=', 'projects_users.projects_id')
            ->where('projects_users.users_id', $user->id)
            ->where('projects_users.as_owner', 'S');

        $sharedNormal = Project::select('projects.*', 'users.name as owner_name')
            ->leftJoin('users', 'projects.users_id', '=', 'users.id')
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

        if ($permittedProjectIds->isNotEmpty()) {
            $permittedProjects = Project::select('projects.*', 'users.name as owner_name')
                ->leftJoin('users', 'projects.users_id', '=', 'users.id')
                ->whereIn('projects.id', $permittedProjectIds);

            return $ownProjects
                ->union($sharedAsOwner)
                ->union($sharedNormal)
                ->union($permittedProjects);
        }

        return $ownProjects
            ->union($sharedAsOwner)
            ->union($sharedNormal);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $controller = $this;

        //
        // Territorios
        //
        $territorios = DB::table('territories')->get();
        $terr = [];
        foreach ($territorios->toArray() as $territorio) {
            $terr[$territorio->id] = ['id' => $territorio->id, 'name' => $territorio->name];
        }

        return view('projects.create', compact('controller', 'terr'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request): RedirectResponse
    {
        $max_size = (int) ini_get('upload_max_filesize') * 1024 * 1024;
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:Film,Serie',
            'season' => 'nullable|integer',
            'episode' => 'nullable|integer',
        ]);

        $file = $request->file('filename');
        $prj = $request->all();
        if ($file && $file->isValid()) {
            $file_name = time().'.'.$file->extension();
            $file->move(public_path('uploads'), $file_name);
            $prj['original_filename'] = $prj['filename'];
            $prj['filename'] = $file_name;
        } else {
            unset($prj['filename']);
        }

        $coverFile = $request->file('cover');
        if ($coverFile && $coverFile->isValid()) {
            $cover_name = 'cover_'.time().'.'.$coverFile->extension();
            $coverFile->move(public_path('uploads'), $cover_name);
            $prj['cover'] = $cover_name;
        } else {
            unset($prj['cover']);
        }

        $prj['users_id'] = auth()->user()->id;
        unset($prj['_token']);
        Project::create($prj);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  Project  $project
     * @return Response
     */
    public function inform($id)
    {
        $controller = $this;
        $obj = DB::table('hotpoints')
            ->select([
                DB::raw('hotpoints.products_id AS pr_id'),
                DB::raw('products.filename AS pr_image'),
                DB::raw('products.name AS pr_name'),
                DB::raw('brands.id AS br_id'),
                DB::raw('brands.filename AS br_logo'),
                DB::raw('brands.name AS br_name'),
                DB::raw('COUNT(*) AS veces'),
            ])
            ->where('versions_id', '=', $id)
            ->leftJoin('products', 'products.id', '=', 'hotpoints.products_id')
            ->leftJoin('brands', 'products.brands_id', '=', 'brands.id')
            ->groupBy('products_id')
            ->orderBy('veces', 'DESC');
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
        foreach ($obj2 as $key => $pr) {
            if ($pr->pr_id != '0') {
                $obj = DB::table('hotpoints')
                    ->where('versions_id', '=', $id)
                    ->where('products_id', '=', $pr->pr_id);
                $obj3 = $obj
                    ->get()
                    ->toArray();
                $tiempos = [];
                foreach ($obj3 as $e) {
                    $tiempos[round($e->time, 0)] = '*';
                }
                $obj2[$key]->veces = ProjectController::seconds2time(count($tiempos));
            }
        }

        return view('projects.inform', compact('obj2', 'controller'));
    }

    public function seconds2time($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);
        $timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

        return $timeFormat;
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Project $project)
    {
        $controller = $this;

        return view('projects.show', compact('project', 'controller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Request $request, Project $project)
    {
        if (! ProjectPermissionHelper::canAccess(auth()->user(), $project, 'read')) {
            abort(403, 'You do not have permission to access this project.');
        }
        $url = $request->url();
        if ($request->field_to_delete != '') {
            $project->update([$request->field_to_delete => null]);
        }
        if (isset($_GET['add'])) {
            DB::table('versions_tags')->insert(
                ['versions_id' => $project->id, 'tags_id' => $_GET['add']]
            );
            header('Location: '.$url);
        }
        if (isset($_GET['remove'])) {
            DB::table('versions_tags')
                ->where([
                    ['id', '=', $_GET['remove']],
                ])
                ->delete();
            header('Location: '.$url);
        }
        if (isset($_GET['change_status'])) {
            $status = ($_GET['status'] == '0' ? '1' : '0');
            DB::table('versions_tags')
                ->where([
                    ['id', '=', $_GET['change_status']],
                ])
                ->update(['disabled' => $status]);
            header('Location: '.$url);
        }

        $video = URL::asset('uploads/'.$project->filename);
        $video_path = public_path('uploads/'.$project->filename);
        $videoInfo = getVideoInfo($video_path);
        $video_fps = $videoInfo['fps'];
        $video_w = $videoInfo['width'];
        $video_h = $videoInfo['height'];

        //
        // Tags vinculados y disponibles
        //
        $controller = $this;
        $all_tags = DB::table('tags')->get();
        $vinculated_tags = DB::table('versions_tags')
            ->select('versions_tags.*', 'tags.name as name')
            ->leftJoin('tags', 'versions_tags.tags_id', '=', 'tags.id')
            ->where('versions_tags.versions_id', $project->id)
            ->get();
        $tags = [];
        $vinculated = [];
        foreach ($all_tags->toArray() as $tag) {
            $tags[$tag->id] = $tag;
        }
        foreach ($vinculated_tags->toArray() as $tag) {
            $vinculated[$tag->tags_id] = $tag;
            if (isset($tags[$tag->tags_id])) {
                unset($tags[$tag->tags_id]);
            }
        }

        //
        // Productos (solo id y name)
        //
        $productos = DB::table('products')
            ->select('id', 'name')
            ->get()
            ->keyBy('id')
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])
            ->toArray();

        //
        // Hotpoints
        //
        $hotpoints = IwantitController::load_hotpoints($project->id);

        //
        // Territorios
        //
        $territories = DB::table('territories')->get();
        $terr = [];
        foreach ($territories->toArray() as $territory) {
            $terr[$territory->id] = ['id' => $territory->id, 'name' => $territory->name];
        }

        //
        // Usuarios por proyecto (sin recargar el proyecto de DB)
        //
        $ubp = $this->getUsersByProjectDirect($project);

        //
        // Licencias que nos son licencias sino que son keyfile (sin reescribir ficheros)
        //
        $kf = self::getFileKeyList($project->id);

        //
        // Detecciones de datision por proyecto
        //
        $datision = DatisionController::getProjectObjects($project->id);

        $distance_frames = 0;

        // --- Parámetros de datision y clases IA en una sola sección ---
        $datisionParams = DB::table('datision_parameters')->first();
        $ai_url = $datisionParams->machine_url ?? null;
        $threshold_secs = $datisionParams->threshold_sec ?? null;
        $ia_clases = DatisionObjectsIaClass::query()
            ->orderBy('name', 'asc')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // --------------------------------------------------------

        $tmp = Hotpoint::getGroupedHotpoints($project->id);
        $objects = [];
        foreach ($tmp->toArray() as $data) {
            $key = $data['products_id'];
            if (! isset($objects[$key])) {
                $objects[$key] = [];
            }
            $objects[$key] = $data['time_groups']->toArray();
        }
        // Batch: cargar todos los productos con sus brands y clases IA en 3 queries
        $productIds = array_keys($objects);
        $allProducts = Product::with(['brand', 'iaClasses'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        // Batch: cargar todos los HotpointsDates del proyecto en 1 query
        $allHotpointsDates = HotpointsDate::where('project_id', $project->id)
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        foreach ($objects as $object_id => $veces) {
            $ttime = 0;
            foreach ($veces as $key => $data) {
                $last = end($data);
                $first = reset($data);
                $time = $last['time'] - $first['time'];
                $ttime += $time;
                $veces[$key] = [
                    'time' => $time,
                    'veces' => $data,
                ];
            }
            $prd = $allProducts->get($object_id);

            // Si el producto no existe, saltamos esta iteración
            if (! $prd) {
                unset($objects[$object_id]);

                continue;
            }

            $brd = $prd->brand;
            $hpd = $allHotpointsDates->get($object_id);
            $estado = 'Enabled';
            $precio = '';
            $precio_s = '';
            $date_in = '';
            $date_out = '';
            $url = '';
            if ($hpd) {
                $estado = $hpd->getEstadoTextAttribute();
                $precio = $hpd->getPriceFormattedAttribute(ceil($ttime), 0);
                $precio_s = $hpd->getPriceRawRounded();
                $date_in = $hpd->get_date_in();
                $date_out = $hpd->get_date_out();
                $url = $hpd->url;
            }
            $clsname = $prd->iaClasses->pluck('name')->implode(', ');
            $objects[$object_id] = [
                'id' => $object_id,
                'thumbnail' => 'uploads/'.$prd->filename,
                'thumbnail_brand' => 'uploads/'.($brd ? $brd->filename : ''),
                'name' => $prd->name,
                'family' => $clsname,
                'brand' => $brd ? $brd->name : '',
                'time' => formatSecondsToTime($ttime),
                'segundos' => ceil($ttime),
                'estado' => $estado,
                'precio' => $precio == '' ? 'No price' : $precio,
                'precio_s' => $precio_s == '' ? '0' : $precio_s,
                'date_in' => $date_in == '' ? '---' : $date_in,
                'date_out' => $date_out == '' ? '---' : $date_out,
                'url' => $url,
                'url_brand' => $brd ? $brd->url : '',
                'veces' => count($veces),
                'data' => $veces,
            ];
        }

        return view('projects.edit', compact('project', 'controller', 'video', 'video_path', 'video_fps', 'video_w', 'video_h', 'hotpoints', 'productos', 'terr', 'kf', 'ubp', 'datision', 'distance_frames', 'ai_url', 'threshold_secs', 'ia_clases', 'objects'));
    }

    public static function generateFileKey($project_id)
    {
        $kf = DB::table('licenses')
            ->where('versions_id', $project_id)
            ->get();
        $base = public_path().'/keyfile/';

        $project = Project::find($project_id);
        $duration = 0.0;
        if ($project && $project->filename) {
            $videoPath = public_path('uploads/'.$project->filename);
            $duration = getVideoDuration($videoPath);
        }

        foreach ($kf as $k => $file) {
            // $fn = md5( $file->key ) . '-iwik.xml';
            $fn = self::cleanFileName($file->name);
            $kf[$k]->fn = $fn;
            $keyContent = str_pad($project_id, 7, '0', STR_PAD_LEFT).$file->key;
            if ($duration > 0) {
                $fileContent = generateSrtContent($duration, $keyContent);
            } else {
                $fileContent = "1\r\n00:00:00,000 --> 00:00:00,000\r\n".$keyContent."\r\n\r\n";
            }
            file_put_contents($base.$fn, $fileContent);
        }

        return $kf;
    }

    /**
     * Versión ligera: solo devuelve los datos de licencias con el nombre de fichero,
     * sin reescribir los ficheros SRT a disco (eso se hace al descargar).
     */
    public static function getFileKeyList($project_id)
    {
        $kf = DB::table('licenses')
            ->where('versions_id', $project_id)
            ->get();

        foreach ($kf as $k => $file) {
            $kf[$k]->fn = self::cleanFileName($file->name);
        }

        return $kf;
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
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
        if (! ProjectPermissionHelper::canAccess(auth()->user(), $project, 'write')) {
            abort(403, 'You do not have permission to modify this project.');
        }

        $request->validate([
            'name' => 'required',
            // otros campos...
        ]);

        // Copia editable de los datos, sin el _token
        $prj = $request->except('_token');

        // Quitar UploadedFiles no válidos (inputs file enviados vacíos por el navegador)
        foreach ($prj as $key => $value) {
            if ($value instanceof UploadedFile && ! $value->isValid()) {
                unset($prj[$key]);
            }
        }

        // ¿Subieron vídeo nuevo?
        if ($request->hasFile('filename') && $request->file('filename')->isValid()) {

            // 1) Borrar el antiguo con seguridad
            //    (esperamos que te llegue un input hidden 'old_video' con el nombre del archivo antiguo)
            if (! empty($prj['old_video'])) {
                $oldPath = public_path('uploads/'.$prj['old_video']);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                } else {
                    // opcional: log para saber por qué no existe
                    \Log::warning('Old video not found for unlink', ['path' => $oldPath]);
                }
            }

            // 2) Guardar el nuevo
            $file = $request->file('filename');
            $ext = $file->getClientOriginalExtension(); // o $file->extension()
            $fileName = time().'.'.strtolower($ext);

            // mueve a public/uploads
            $file->move(public_path('uploads'), $fileName);

            // 3) Actualizar campos
            $prj['original_filename'] = $prj['filename'] ?? $project->filename; // si quieres conservar el nombre previo
            $prj['filename'] = $fileName;
        }

        // ¿Subieron imagen de cover nueva?
        if ($request->hasFile('cover') && $request->file('cover')->isValid()) {
            try {
                if (! empty($prj['old_img'])) {
                    $oldPath = public_path('uploads/'.$prj['old_img']);
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $coverFile = $request->file('cover');
                $coverExt = $coverFile->getClientOriginalExtension();
                $coverName = 'cover_'.time().'.'.strtolower($coverExt);
                $coverFile->move(public_path('uploads'), $coverName);
                $prj['cover'] = $coverName;
            } catch (\Exception $e) {
                \Log::error('Error al procesar cover: '.$e->getMessage(), [
                    'exception' => get_class($e),
                    'project_id' => $project->id,
                ]);
                throw $e;
            }
        }

        // Aplicar cambios al modelo
        try {
            $project->update($prj);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar proyecto ID '.$project->id.': '.$e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'prj_keys' => array_keys($prj),
                'cover' => $prj['cover'] ?? 'no cover',
            ]);
            throw $e;
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }

    public function getParams($data = '')
    {
        $params = [];
        $params['view'] = 'projects';
        $params['singular'] = 'Project';
        $params['plural'] = 'projects';
        $params['fields'] = [
            [
                'label' => 'ID',
                'name' => 'id',
                'editable' => false,
                'orderby' => true,
            ],
            [
                'label' => 'Owner',
                'name' => 'owner_name',
                'editable' => false,
                'orderby' => true,
                'nbsp' => true,
            ],
            [
                'label' => 'Name',
                'name' => 'name',
                'editable' => true,
                'type' => 'text',
                'orderby' => true,
                'nbsp' => true,
            ],
            [
                'label' => 'Demo Code',
                'name' => 'demo_code',
                'editable' => true,
                'type' => 'text',
                'orderby' => false,
                'hide_on_index' => false,
            ],
            [
                'label' => 'Territories',
                'name' => 'territories_id',
                'editable' => true,
                'type' => 'select',
                'format' => 'related',
            ],
            [
                'label' => 'Type',
                'name' => 'type',
                'editable' => true,
                'type' => 'select',
                'format' => 'related',
                'values' => ['Film', 'Serie'],
            ],
            [
                'label' => 'Season',
                'name' => 'season',
                'editable' => true,
                'type' => 'text',
                'show_when' => [
                    'field' => 'type',
                    'value' => 'Serie',
                ],
            ],
            [
                'label' => 'Episode',
                'name' => 'episode',
                'editable' => true,
                'type' => 'text',
                'show_when' => [
                    'field' => 'type',
                    'value' => 'Serie',
                ],
            ],
            [
                'label' => 'State',
                'name' => 'ai_task_id',
                'editable' => false,
                'orderby' => false,
            ],
            [
                'label' => 'Filename',
                'name' => 'filename',
                'editable' => true,
                'type' => 'file',
                'hide_on_index' => true,
            ],
            [
                'label' => 'Cover Image',
                'name' => 'cover',
                'editable' => true,
                'type' => 'image',
                'hide_on_index' => true,
                'txt_button' => 'Change the demo cover',
            ],
        ];
        $ret = $params;
        if ($data != '' && isset($params[$data])) {
            $ret = $params[$data];
        }

        return $ret;
    }

    public function getText($id = '')
    {
        $text = [
            'left_column' => 'Available tags',
            'left_column_button' => 'Add tag to project',
            'right_column' => 'Tag related to this project',
            'right_column_button' => 'Remove tag from project',
        ];
        if ($id != '') {
            $text = $text[$id];
        }

        return $text;
    }

    public static function cleanFileName($file_name)
    {
        $file_name .= '-iwik.srt';
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_name_str = pathinfo($file_name, PATHINFO_FILENAME);

        // Replaces all spaces with hyphens.
        $file_name_str = str_replace(' ', '-', $file_name_str);
        // Removes special chars.
        $file_name_str = preg_replace('/[^A-Za-z0-9\-\_]/', '', $file_name_str);
        // Replaces multiple hyphens with single one.
        $file_name_str = preg_replace('/-+/', '-', $file_name_str);

        $clean_file_name = $file_name_str.'.'.$file_ext;

        return strtolower($clean_file_name);
    }
}
