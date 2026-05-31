<?php

namespace App\Http\Controllers;

use App\Models\Options;
use App\Models\Project;
use App\Models\User;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
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
            $perPage = 20;
        }

        $currentUser = auth()->user();
        $userRole = $currentUser->roles()->first();

        $users = User::query();

        if (strlen($search) >= 3) {
            $users->where('name', 'like', '%'.$search.'%');
        }

        if (isset($_GET['orderby'])) {
            $order = $_GET['ordertype'] ?? 'asc';
            $users->orderBy($_GET['orderby'], $order);
        }

        if (! $userRole || ! $userRole->can_manage_all_users) {
            if ($userRole && $userRole->can_manage_own_users) {
                $users->where('client_id', $currentUser->id);
            } else {
                $users->where('id', $currentUser->id);
            }
        }

        if ($perPage === 9999) {
            $users = $users->get();
            $users = new LengthAwarePaginator($users, $users->count(), $users->count());
        } else {
            $users = $users->paginate($perPage);
        }

        $refs = User::latest()->get();
        $supers = [];
        foreach ($refs as $ref) {
            $supers[$ref->id] = ['id' => $ref->id, 'name' => $ref->name];
        }

        $controller = $this;

        return view('users.index', compact('users', 'controller', 'supers'))->with('i', (request()->input('page', 1) - 1) * $perPage);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $options = Options::all()->groupBy('type');

        return view('users.create', compact('options'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,super,editor',
        ]);

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role = $request->input('role');
        $user->save();

        $this->updateUserOptions($user, $request->all());

        return to_route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(User $user)
    {
        $controller = $this;

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(User $user)
    {
        $controller = $this;

        return view('users.edit', compact('user', 'controller'));

        //        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);
        $data = $request->all();
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        if (isset($data['email'])) {
            unset($data['emmail']);
        }
        unset($data['_token']);
        unset($data['role']);
        unset($data['project_permissions']);
        $user->update($data);

        if ($request->has('role')) {
            $this->syncRoles($user, $request->input('role'));
        }

        if ($request->has('project_permissions')) {
            $this->syncProjectPermissions($user, $request->input('project_permissions'));
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    protected function syncRoles(User $user, string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->syncRoles([$role]);
        }
    }

    protected function syncProjectPermissions(User $user, array $permissions): void
    {
        DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->delete();

        foreach ($permissions as $projectId => $accessLevel) {
            if ($accessLevel !== 'none') {
                DB::table('project_user_permissions')->insert([
                    'user_id' => $user->id,
                    'project_id' => $projectId,
                    'access_level' => $accessLevel,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Actualiza las opciones del usuario.
     *
     * @return void
     */
    public function updateUserOptions(User $user, array $data)
    {
        $userId = $user->id;

        // Eliminar todos los registros existentes para el usuario
        UserOption::where('user_id', $userId)->delete();

        // Insertar los nuevos registros
        if (isset($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $optionId) {
                UserOption::create([
                    'user_id' => $userId,
                    'option_id' => $optionId,
                    'active' => '1',
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function getParams($data = '')
    {
        $params = [];
        $params['view'] = 'users';
        $params['singular'] = 'user';
        $params['plural'] = 'users';
        $params['fields'] = [
            [
                'label' => 'ID',
                'name' => 'id',
                'editable' => false,
                'orderby' => true,
            ],
            [
                'label' => 'Name',
                'name' => 'name',
                'editable' => true,
                'type' => 'text',
                'orderby' => true,
            ],
            [
                'label' => 'E-mail',
                'name' => 'email',
                'editable' => true,
                'type' => 'email',
            ],
            [
                'label' => 'Role',
                'name' => 'role',
                'editable' => true,
                'type' => 'text',
            ],
            [
                'label' => 'Supervisor',
                'name' => 'client_id',
                'editable' => true,
                'type' => 'select',
                'format' => 'related',
                'hide_on_index' => true,
            ],
            [
                'label' => 'Password',
                'name' => 'password',
                'editable' => true,
                'type' => 'text',
                'hide_on_index' => true,
            ],
        ];
        $ret = $params;
        if ($data != '' && isset($params[$data])) {
            $ret = $params[$data];
        }

        return $ret;
    }

    public function searchProjects(Request $request)
    {
        $search = $request->get('search', '');
        $userId = $request->get('user_id');

        $query = Project::select('id', 'name', 'type', 'season', 'episode')
            ->orderBy('id', 'desc');

        if (strlen($search) >= 3) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $projects = $query->limit(20)->get();

        $userPermissions = DB::table('project_user_permissions')
            ->where('user_id', $userId)
            ->pluck('access_level', 'project_id');

        $html = '';
        foreach ($projects as $project) {
            $typeLabel = $project->type;
            if ($project->type === 'Serie') {
                $typeLabel .= ' (S'.($project->season ?? '?').' E'.($project->episode ?? '?').')';
            }
            $currentLevel = $userPermissions->get($project->id, 'none');
            $html .= '<tr>';
            $html .= '<td>'.e($project->name).' <span class="text-muted">['.e($typeLabel).']</span></td>';
            $html .= '<td>';
            $html .= '<select name="project_permissions['.$project->id.']" class="form-control form-control-sm project-permission-select" data-project-id="'.$project->id.'">';
            $html .= '<option value="none"'.($currentLevel == 'none' ? ' selected' : '').'>No Access</option>';
            $html .= '<option value="read"'.($currentLevel == 'read' ? ' selected' : '').'>Read</option>';
            $html .= '<option value="write"'.($currentLevel == 'write' ? ' selected' : '').'>Write</option>';
            $html .= '<option value="create"'.($currentLevel == 'create' ? ' selected' : '').'>Create</option>';
            $html .= '</select>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);
    }
}
