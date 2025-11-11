<?php

/*
ALTER TABLE `products`
ADD COLUMN `icono` VARCHAR(4096) NULL AFTER `filename`;
*/

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\DatisionObjectsIaClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $products = Product::latest();
        if ( isset( $_GET[ 'orderby' ] ) ) {
            $order = 'asc';
            if ( isset( $_GET[ 'ordertype' ] ) ) {
                $order = $_GET[ 'ordertype' ];
            }
            $products = Product::orderBy( $_GET[ 'orderby' ], $order )->latest();
        }
        $products = $products->paginate(300);
//        $products = Product::latest()->paginate(300);
        $controller = $this;
        $marcas = DB::table('brands')->get();
        $brands = [];
        foreach( $marcas->toArray() as $marca ) {
            $brands[ $marca->id ] = [ 'id' => $marca->id, 'name' => $marca->name ];
        }
        return view('products.index', compact('products', 'controller','brands'))->with('i', (request()->input('page', 1) - 1) * 300);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $controller = $this;

        //
        // Marcas
        //
        $marcas = DB::table('brands')->get();
        $brands = [];
        foreach( $marcas->toArray() as $marca ) {
            $brands[ $marca->id ] = [ 'id' => $marca->id, 'name' => $marca->name ];
        }

        return view('products.create', compact('controller', 'brands'));
    }

    /**
     * Store a newly created resource in storage (versión AJAX).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        echo '<pre>';
        print_r( $request->all() );
        echo '</pre>';
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'brands_id' => 'nullable|exists:brands,id',
                'url' => 'nullable|url',
                'disabled' => 'nullable|in:0,1',
                'auto_open' => 'nullable|in:0,1',
                'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'icono' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $productData = $request->only(['name', 'brands_id', 'url', 'disabled', 'auto_open']);

            // Manejar subida de imagen principal
            if ($request->hasFile('filename')) {
                $file = $request->file('filename');
                $fileName = time() . '_img.' . $file->extension();
                $file->move(public_path('uploads'), $fileName);
                $productData['filename'] = $fileName;
            }

            // Manejar subida de icono
            if ($request->hasFile('icono')) {
                $file = $request->file('icono');
                $fileName = time() . '_icon.' . $file->extension();
                $file->move(public_path('uploads'), $fileName);
                $productData['icono'] = $fileName;
            }

            $product = Product::create($productData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully',
                    'product_id' => $product->id,
                    'product' => $product
                ]);
            }

            return redirect()->route('products.index')->with('success', 'Product created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating product: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error creating product')->withInput();
        }
    }

    /**
     * Asociar una clase IA al producto
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function associateIaClass(Request $request, $productId)
    {
        try {
            $request->validate([
                'ia_class_id' => 'required|exists:datision_objects_ia_classes,id'
            ]);

            $product = Product::findOrFail($productId);
            
            // Verificar si ya existe la asociación
            if (!$product->iaClasses()->where('datision_objects_ia_classes_id', $request->ia_class_id)->exists()) {
                $product->iaClasses()->attach($request->ia_class_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'IA Class associated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error associating IA class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product) {
        $controller = $this;
        return view('products.show', compact('product', 'controller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Product $product) {

        if ( '' != $request->field_to_delete ) {
            // Elimino imagen
            $product->update( [ $request->field_to_delete => null ] );
        }

        $url = $request->url();
        if ( isset( $_GET[ 'add' ] ) ) {
            DB::table('products_tags')->insert(
                ['products_id' => $product->id, 'tags_id' => $_GET[ 'add' ]]
            );
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'remove' ] ) ) {
            DB::table('products_tags')
                ->where( [
                    [ 'id', '=', $_GET[ 'remove' ] ]
                ] )
                ->delete();
            header('Location: ' . $url);
        }
        if ( isset( $_GET[ 'change_status' ] ) ) {
            $status = ( $_GET[ 'status' ] == '0' ? '1' : '0' );
            DB::table('products_tags')
                ->where( [
                    [ 'id', '=', $_GET[ 'change_status' ] ]
                ] )
                ->update( [ 'disabled' => $status ] );
            header('Location: ' . $url);
        }

        //
        // Tags vinculados y disponibles
        //
        $controller = $this;
        $all_tags = DB::table('tags')->get();
        $vinculated_tags = DB::table('products_tags')
            ->select( 'products_tags.*', 'tags.name as name')
            ->leftJoin('tags', 'products_tags.tags_id', '=', 'tags.id')
            ->where('products_tags.products_id', $product->id )
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
        $marcas = DB::table('brands')->get();
        $brands = [];
        foreach( $marcas->toArray() as $marca ) {
            $brands[ $marca->id ] = [ 'id' => $marca->id, 'name' => $marca->name ];
        }

        /* --- IDs que ya están en el pivot --- */
        $activeIds = DB::table('products_datision_objects_ia_classes')
            ->where('products_id', $product->id)
            ->pluck('datision_objects_ia_classes_id')
            ->toArray();

        /* --- Todas las clases ordenadas por nombre --- */
        $all = DatisionObjectsIaClass::select('id', 'name')
                ->orderBy('name')
                ->get();

        /* --- Particionar la colección en “seleccionadas” vs “disponibles” --- */
        [$selected, $available] = $all->partition(
            fn ($c) => in_array($c->id, $activeIds)   // true  → va a $selected
        );

        /* --- Convertir a array si lo necesitas --- */
        $ia_selected_classes  = $selected->values()->toArray();
        $ia_available_classes = $available->values()->toArray();

        return view('products.edit', compact('product','controller','tags','vinculated','url','brands','ia_selected_classes', 'ia_available_classes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product) {
        $request->validate([
            'name' => 'required',
        ]);
        $file = $request->file('filename');
        $prj = $request->all();
        $upload_image = false;
        $upload_icono = false;
        if ( !is_null( $file ) ) {
            $file_name = time() . '.' . $file->extension();
            $file->move( public_path('uploads'), $file_name );
            $prj[ 'original_filename' ] = $prj[ 'filename' ];
            $prj[ 'filename' ] = $file_name;
            $upload_image = true;
        }
        $file = $request->file('icono');
        if ( !is_null( $file ) ) {
            $file_name = time() . '.' . $file->extension();
            $file->move( public_path('uploads'), $file_name );
            $prj[ 'icono' ] = $file_name;
            $upload_icono = true;
        }
        if ( !$upload_image ) {
            unset( $prj[ 'filename' ] );
            unset( $prj[ 'original_filename' ] );
        }
        if ( !$upload_icono ) {
            unset( $prj[ 'icono' ] );
        }
        unset( $prj['_token'] );
        $product->update($prj);
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product) {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'products';
        $params[ 'singular' ] = 'product';
        $params[ 'plural' ] = 'products';
        $params[ 'fields' ] = [
            [
                'label' => 'ID',
                'name' => 'id',
                'editable' => false,
                'orderby' => true
            ],
            [
                'label' => 'Brand',
                'name' => 'brands_id',
                'editable' => true,
                'type' => 'select',
                'format' => 'related',
                'orderby' => true
            ],
            [
                'label' => 'Name',
                'name' => 'name',
                'editable' => true,
                'type' => 'text',
                'orderby' => true,
                'force_nbsp' => true,
            ],
            [
                'label' => 'Status',
                'name' => 'disabled',
                'editable' => true,
                'hide_on_index' => true,
                'type' => 'select',
                'format' => 'switch'
            ],
            [
                'label' => 'Auto open url',
                'name' => 'auto_open',
                'editable' => true,
                'hide_on_index' => true,
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
                'label' => 'Icon hotpoint',
                'name' => 'icono',
                'editable' => true,
                'hide_on_index' => true,
                'type' => 'image',
                'extra_class' => 'icono_product',
                'txt_button' => 'Change the icon hotpoint'
            ],
            [
                'label' => 'Image',
                'name' => 'filename',
                'editable' => true,
                'hide_on_index' => true,
                'type' => 'image',
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
            'left_column' => 'Available tags',
            'left_column_button' => 'Add tag to product',
            'right_column' => 'Tag related to this product',
            'right_column_button' => 'Remove tag from product',
        ];
        if  ( '' != $id ) {
            $text = $text[ $id ];
        }
        return $text;
    }
}
