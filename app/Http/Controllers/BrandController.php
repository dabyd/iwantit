<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $brands = Brand::latest();
        if ( isset( $_GET[ 'orderby' ] ) ) {
            $order = 'asc';
            if ( isset( $_GET[ 'ordertype' ] ) ) {
                $order = $_GET[ 'ordertype' ];
            }

            $brands = Brand::orderBy( $_GET[ 'orderby' ], $order )->latest();
        }
        $brands = $brands->paginate(300);
//        $brands = Brand::latest()->paginate(5);
        $controller = $this;
        return view('brands.index', compact('brands','controller'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $controller = $this;
        return view('brands.create', compact('controller'));
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
        unset( $prj['_token'] );

        Brand::create($prj);
        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand) {
        $controller = $this;
        return view('brands.show', compact('controller','brand'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Brand $brand) {
        $url = $request->url();
        if ( isset( $_GET[ 'add' ] ) ) {
            DB::table('products')
                ->where( [
                    [ 'id', '=', $_GET[ 'add' ] ]
                ] )
                ->update( [ 'brands_id' => $brand->id ] );
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'remove' ] ) ) {
            DB::table('products')
                ->where( [
                    [ 'id', '=', $_GET[ 'remove' ] ]
                ] )
                ->update( [ 'brands_id' => null ] );
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'change_status' ] ) ) {
/*
            $status = ( $_GET[ 'status' ] == '0' ? '1' : '0' );
            DB::table('brands_tags')
                ->where( [
                    [ 'id', '=', $_GET[ 'change_status' ] ]
                ] )
                ->update( [ 'disabled' => $status ] );
*/
            header('Location: ' . $url);
        }


        //
        // Productos vinculados y disponibles
        //
        $controller = $this;
        $all_prods = DB::table('products')->get();
        $selected_prods = DB::table('brands')
            ->select( 'brands.*', 'products.name', 'products.id' )
            ->leftJoin('products', 'products.brands_id', '=', 'brands.id')
            ->where('brands.id', $brand->id )
            ->get();
        $prods = [];
        $selected = [];

        $sql = '
        SELECT
            products.*, brands.name AS b_name, brands.id AS b_id
        FROM
            products
            LEFT JOIN brands ON brands.id = products.brands_id
        ';
        $all_prods = DB::select($sql);

        foreach( $all_prods as $prod ) {
            if ( '' != $prod->b_id ) {
                $prod->name = $prod->name . ' (linked to: ' . $prod->b_name . ')';
            }
            $prods[ $prod->id ] = $prod;
        }
        foreach( $selected_prods->toArray() as $prod ) {
            $selected[ $prod->id ] = $prod;
            if ( isset( $prods[ $prod->id ] ) ) {
                unset( $prods[ $prod->id ] );
            }
        }

        return view('brands.edit', compact('brand','controller','prods','selected','url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand) {
        $max_size = (int) ini_get('upload_max_filesize') * 1024 * 1024;
        $request->validate([
            'name' => 'required',
        ]);
        $file = $request->file('filename');
        $prj = $request->all();
        if ( !is_null( $file ) ) {
            $file_name = time() . '.' . $file->extension();
            $file->move( public_path('uploads'), $file_name );
            $prj[ 'original_filename' ] = $prj[ 'filename' ];
            $prj[ 'filename' ] = $file_name;
        }
        unset( $prj['_token'] );
        $brand->update($prj);
        return redirect()->route('brands.index')->with('success', 'Brand updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand) {
        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully');
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'brands';
        $params[ 'singular' ] = 'brand';
        $params[ 'plural' ] = 'brands';
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
                'label' => 'Status',
                'name' => 'disabled',
                'editable' => true,
                'type' => 'select',
                'format' => 'switch'
            ],
            [
                'label' => 'URL',
                'name' => 'url',
                'editable' => true,
                'hide_on_index' => true,
                'type' => 'text',
            ],
            [
                'label' => 'Logo',
                'name' => 'filename',
                'editable' => true,
                'hide_on_index' => true,
                'type' => 'image'
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
            'left_column' => 'Available products',
            'left_column_button' => 'Add product to brand',
            'right_column' => 'Products related to this brand',
            'right_column_button' => 'Remove product from brand',
        ];
        if  ( '' != $id ) {
            $text = $text[ $id ];
        }
        return $text;
    }

}
