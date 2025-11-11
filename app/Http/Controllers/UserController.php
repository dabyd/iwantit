<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $users = User::latest();
        if ( isset( $_GET[ 'orderby' ] ) ) {
            $order = 'asc';
            if ( isset( $_GET[ 'ordertype' ] ) ) {
                $order = $_GET[ 'ordertype' ];
            }

            $users = User::orderBy( $_GET[ 'orderby' ], $order )->latest();
        }
        $users = $users->paginate(300);
        $refs = User::latest()->get();
        $supers = [];
        foreach ($refs as $ref) {
            $supers[$ref->id] = [ 'id' => $ref->id, 'name' => $ref->name];
        }


//         $users = User::latest()->paginate(300);
        $controller = $this;

        return view('users.index', compact('users','controller','supers'))->with('i', (request()->input('page', 1) - 1) * 300);

//        return view('territories.index', compact('territories', 'controller'))->with('i', (request()->input('page', 1) - 1) * 300);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $controller = $this;
        return view('users.create', compact('controller'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make( $request->input('password') );
        $user->save();

        return to_route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {
        $controller = $this;
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user) {
        $controller = $this;

        return view('users.edit', compact('user','controller'));

//        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);
        $data = $request->all();
        if ( empty( $data[ 'password' ] ) ) {
            unset( $data['password']);
        } else {
            $data[ 'password' ] = Hash::make( $data[ 'password' ] );
        }
        if ( isset( $data[ 'email' ] ) ) {
            unset( $data['emmail']);
        }
        unset( $data['_token'] );        
        $user->update($data);

        // Actualizar las opciones del usuario
        $this->updateUserOptions($user, $data);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Actualiza las opciones del usuario.
     *
     * @param array $data
     * @return void
     */
    public function updateUserOptions(User $user, array $data) {
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'users';
        $params[ 'singular' ] = 'user';
        $params[ 'plural' ] = 'users';
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
                'orderby' => true
            ],
            [
                'label' => 'E-mail',
                'name' => 'email',
                'editable' => true,
                'type' => 'email'
            ],
            [
                'label' => 'Role',
                'name' => 'role',
                'editable' => true,
                'type' => 'text'
            ],
            [
                'label' => 'Supervisor',
                'name' => 'client_id',
                'editable' => true,
                'type' => 'select',
                'format' => 'related',
                'hide_on_index' => true
            ],
            [
                'label' => 'Password',
                'name' => 'password',
                'editable' => true,
                'type' => 'text',
                'hide_on_index' => true
            ],
        ];
        $ret = $params;
        if ( '' != $data && isset( $params[ $data ] ) ) {
            $ret = $params[ $data ];
        }
        return $ret;
    }
}
