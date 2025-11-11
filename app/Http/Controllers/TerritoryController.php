<?php

namespace App\Http\Controllers;

use App\Models\Territory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerritoryController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $territories = Territory::latest();
        if ( isset( $_GET[ 'orderby' ] ) ) {
            $order = 'asc';
            if ( isset( $_GET[ 'ordertype' ] ) ) {
                $order = $_GET[ 'ordertype' ];
            }

            $territories = Territory::orderBy( $_GET[ 'orderby' ], $order )->latest();
        }
        $territories = $territories->paginate(300);
//        $territories = Territory::latest()->paginate(300);
        $controller = $this;
        return view('territories.index', compact('territories', 'controller'))->with('i', (request()->input('page', 1) - 1) * 300);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $controller = $this;
        return view('territories.create', compact('controller'));
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
        ]);
        $prj = $request->all();
        unset($prj['_token']);

        Territory::create($prj);
        return redirect()->route('territories.index')->with('success', 'Territory created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Territory  $territory
     * @return \Illuminate\Http\Response
     */
    public function show(Territory $territory) {
        $controller = $this;
        return view('territories.show', compact('territory', 'controller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Territory  $territory
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Territory $territory) {

        $url = $request->url();
        if ( isset( $_GET[ 'add' ] ) ) {
            DB::table('territories_tags')->insert(
                ['territories_id' => $territory->id, 'tags_id' => $_GET[ 'add' ]]
            );
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'remove' ] ) ) {
            DB::table('territories_tags')
                ->where( [
                    [ 'id', '=', $_GET[ 'remove' ] ]
                ] )
                ->delete();
            header('Location: ' . $url);
        }

        //
        // preparo todo lo que he de enviar para relacionar tags y territories
        //
        $controller = $this;
        $all_tags = DB::table('tags')->get();
        $disabled_tags = DB::table('territories_tags')
            ->select( 'territories_tags.*', 'tags.name as name')
            ->leftJoin('tags', 'territories_tags.tags_id', '=', 'tags.id')
            ->where('territories_tags.territories_id', $territory->id )
            ->get();
        $tags = [];
        $disabled = [];
        foreach( $all_tags->toArray() as $tag ) {
            $tags[ $tag->id ] = $tag;
        }
        $disabled = [];
        foreach( $disabled_tags->toArray() as $tag ) {
            $disabled[ $tag->tags_id ] = $tag;
            if ( isset( $tags[ $tag->tags_id ] ) ) {
                unset( $tags[ $tag->tags_id ] );
            }
        }
        return view('territories.edit', compact('territory','controller','tags','disabled','url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Territory  $territory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Territory $territory) {
        $request->validate([
            'name' => 'required',
        ]);
        $prj = $request->all();
        unset( $prj['_token'] );
        $territory->update($prj);
        return redirect()->route('territories.index')->with('success', 'Territory updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Territory  $territory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Territory $territory) {
        $territory->delete();
        return redirect()->route('territories.index')->with('success', 'Territory deleted successfully');
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'territories';
        $params[ 'singular' ] = 'territory';
        $params[ 'plural' ] = 'territories';
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
