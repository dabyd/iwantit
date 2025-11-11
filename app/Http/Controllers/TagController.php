<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $tags = Tag::latest();
        if ( isset( $_GET[ 'orderby' ] ) ) {
            $order = 'asc';
            if ( isset( $_GET[ 'ordertype' ] ) ) {
                $order = $_GET[ 'ordertype' ];
            }

            $tags = Tag::orderBy( $_GET[ 'orderby' ], $order )->latest();
        }
        $tags = $tags->paginate(300);
        $controller = $this;
        return view('tags.index', compact('tags','controller'))->with('i', (request()->input('page', 1) - 1) * 300);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $controller = $this;
        return view('tags.create', compact('controller'));
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

        Tag::create($prj);
        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag) {
        $controller = $this;
        return view('tags.show', compact('tag', 'controller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag) {
        $controller = $this;
        return view('tags.edit', compact('tag','controller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag) {
        $request->validate([
            'name' => 'required',
        ]);
        $prj = $request->all();
        unset( $prj['_token'] );
        $tag->update($prj);
        return redirect()->route('tags.index')->with('success', 'Tag updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag) {
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully');
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'tags';
        $params[ 'singular' ] = 'tag';
        $params[ 'plural' ] = 'tags';
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
                'label' => 'Disabled',
                'name' => 'disabled',
                'editable' => true,
                'type' => 'select',
                'format' => 'switch',
            ]
        ];
        $ret = $params;
        if ( '' != $data && isset( $params[ $data ] ) ) {
            $ret = $params[ $data ];
        }
        return $ret;
    }
}
