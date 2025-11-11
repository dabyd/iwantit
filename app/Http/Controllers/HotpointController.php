<?php

namespace App\Http\Controllers;

use App\Models\Hotpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use App\Models\Project;

class HotpointController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
//        $hotpoints = Hotpoint::latest()->paginate(300);
        $hotpoints = Hotpoint::latest()->get();
        $pelis = [];
        foreach( $hotpoints as $hotpoint ) {
            $hp = $hotpoint->toArray();
            $hp = (object) $hp;
            if ( !isset( $pelis[ $hp->versions_id ] ) ) {
                $pelis[ $hp->versions_id ] = [];
            }
            if ( !isset( $pelis[ $hp->versions_id ][ $hp->products_id ] ) ) {
                $pelis[ $hp->versions_id ][ $hp->products_id ] = [];
            }
            $pelis[ $hp->versions_id ][ $hp->products_id ][] = $hp;
        }
        $projects = [];
        foreach( $pelis as $id => $nada ) {
            $projects[ $id ] = Project::where('id', $id)->value('name');
        }
        $controller = $this;

        $pr = request()->get('pr'); // Obtiene el parámetro 'pr' de la URL
        // Si hay un parámetro 'pr', filtramos los hotpoints, si no, obtenemos todos
        $hotpoints = Hotpoint::latest()
            ->when($pr, fn($query) => $query->where('versions_id', $pr))
            ->get();

//        dd( $pelis );

        return view('hotpoints.index', compact('projects','hotpoints', 'pelis', 'controller'))->with('i', (request()->input('page', 1) - 1) * 300);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $controller = $this;
        return view('hotpoints.create', compact('controller'));
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
            'filename' => [
                'required',
                'max:'.$max_size,
            ]
        ]);

        $file = $request->file('filename');
        $file_name = time() . '.' . $file->extension();
        $file->move( public_path('uploads'), $file_name );
        $prj = $request->all();
        $prj[ 'original_filename' ] = $prj[ 'filename' ];
        $prj[ 'filename' ] = $file_name;
        unset($prj['_token']);
        Hotpoint::create($prj);
        return redirect()->route('hotpoints.index')->with('success', 'Hotpoint created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hotpoint  $hotpoint
     * @return \Illuminate\Http\Response
     */
    public function show(Hotpoint $hotpoint) {
        $controller = $this;
        return view('hotpoints.show', compact('hotpoint', 'controller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hotpoint  $hotpoint
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Hotpoint $hotpoint) {
        /*
        $url = $request->url();
        if ( isset( $_GET[ 'add' ] ) ) {
            DB::table('hotpoints_tags')->insert(
                ['hotpoints_id' => $hotpoint->id, 'tags_id' => $_GET[ 'add' ]]
            );
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'remove' ] ) ) {
            DB::table('hotpoints_tags')
                ->where( [
                    [ 'id', '=', $_GET[ 'remove' ] ]
                ] )
                ->delete();
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'change_status' ] ) ) {
            $status = ( $_GET[ 'status' ] == '0' ? '1' : '0' );
            DB::table('hotpoints_tags')
                ->where( [
                    [ 'id', '=', $_GET[ 'change_status' ] ]
                ] )
                ->update( [ 'disabled' => $status ] );
            header('Location: ' . $url);
        }

        $video = URL::asset('uploads/' . $hotpoint->filename );

        //
        // Tags vinculados y disponibles
        //
        $controller = $this;
        $all_tags = DB::table('tags')->get();
        $vinculated_tags = DB::table('hotpoints_tags')
            ->select( 'hotpoints_tags.*', 'tags.name as name')
            ->leftJoin('tags', 'hotpoints_tags.tags_id', '=', 'tags.id')
            ->where('hotpoints_tags.hotpoints_id', $hotpoint->id )
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
        // Marcas
        //
        $marcas = DB::table('hotpoints')->get();
        $hotpoints = [];
        foreach( $marcas->toArray() as $marca ) {
            $hotpoints[ $marca->id ] = [ 'id' => $marca->id, 'name' => $marca->name ];
        }
        */
        $controller = $this;
        return view('hotpoints.edit', compact('hotpoint','controller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hotpoint  $hotpoint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hotpoint $hotpoint) {
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
        $hotpoint->update($prj);
        return redirect()->route('hotpoints.index')->with('success', 'Hotpoint updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hotpoint  $hotpoint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hotpoint $hotpoint) {
        $hotpoint->delete();
        return redirect()->route('hotpoints.index')->with('success', 'Hotpoint deleted successfully');
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'hotpoints';
        $params[ 'singular' ] = 'Hotpoint';
        $params[ 'plural' ] = 'hotpoints';
        $params[ 'fields' ] = [
            [
                'label' => 'ID',
                'name' => 'id',
                'editable' => false
            ],
            [
                'label' => 'Product',
                'name' => 'products_id',
                'editable' => true,
                'type' => 'text'
            ],
            [
                'label' => 'Version',
                'name' => 'versions_id',
                'editable' => true,
                'type' => 'text'
            ],
            [
                'label' => 'Position X',
                'name' => 'pos_x',
                'editable' => false,
                'type' => 'text'
            ],
            [
                'label' => 'Position Y',
                'name' => 'pos_y',
                'editable' => false,
                'type' => 'text'
            ],
            [
                'label' => 'Time',
                'name' => 'time',
                'editable' => false,
                'type' => 'text'
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
            'left_column_button' => 'Add tag to hotpoint',
            'right_column' => 'Tag related to this hotpoint',
            'right_column_button' => 'Remove tag from hotpoint',
        ];
        if  ( '' != $id ) {
            $text = $text[ $id ];
        }
        return $text;
    }
}
