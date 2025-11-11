<?php

namespace App\Http\Controllers;

use App\Models\Options;
use App\Models\UserOption;
use Illuminate\Http\Request;

class OptionController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $options = Options::all();
        $controller = $this;
        return view('options.index', compact('options','controller'))->with('i', (request()->input('page', 1) - 1) * 300);
/*
        $options = Options::all();
        return view('options.index', compact('options'));
*/
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('options.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        Options::create($request->all());

        return redirect()->route('options.index')->with('success', 'Option created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function show(Options $option) {
        return view('options.show', compact('option'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function edit(Options $option) {
        return view('options.edit', compact('option'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Options $option) {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $option->update($request->all());

        return redirect()->route('options.index')->with('success', 'Option updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Option  $option
     * @return \Illuminate\Http\Response
     */
    public function destroy(Options $option) {
        $option->delete();

        return redirect()->route('options.index')->with('success', 'Option deleted successfully.');
    }

    /**
     * Check if an option with the given name and type exists, and create it if it doesn't.
     *
     * @param  string  $option
     * @param  string  $type
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function canAccess($option, $type, $user) {
        $existingOption = Options::where('name', $option)->where('type', $type)->first();

        if (!$existingOption) {
            Options::create([
                'name' => $option,
                'type' => $type,
                // 'user_id' => $user_id, // Si tienes una columna user_id en la tabla options
            ]);
        }

        $puede = false;
        if ( 'admin' == $user->role ) {
            $puede = true;
        } else {
            // Obtener la opción desde la tabla options
            $existingOption = Options::where('name', $option)->where('type', $type)->first();

            if (!$existingOption) {
                $existingOption = Options::create([
                    'name' => $option,
                    'type' => $type,
                ]);
            }

            // Buscar en la tabla user_options
            $userOption = UserOption::where('user_id', $user->id)
                                    ->where('option_id', $existingOption->id)
                                    ->first();

            // Verificar si el usuario tiene acceso
            if ($userOption && $userOption->active) {
                $puede = true;
            }
        }

        return $puede;
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'options';
        $params[ 'singular' ] = 'option';
        $params[ 'plural' ] = 'options';
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
                'label' => 'Type',
                'name' => 'type',
                'editable' => true,
                'type' => 'text',
                'orderby' => true
            ],
        ];
        $ret = $params;
        if ( '' != $data && isset( $params[ $data ] ) ) {
            $ret = $params[ $data ];
        }
        return $ret;
    }

    public function getText( $id = '' ) {
        $text = [
            'left_column' => 'Enabled tags for this territory',
            'left_column_button' => 'Disable tag',
            'right_column' => 'Disabled tags for this territory',
            'right_column_button' => 'Enable tag',
        ];
        if  ( '' != $id ) {
            $text = $text[ $id ];
        }
        return $text;
    }
}