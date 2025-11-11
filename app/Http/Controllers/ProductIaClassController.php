<?php
// app/Http/Controllers/ProductIaClassController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\DatisionObjectsIaClass;
use Illuminate\Support\Facades\DB;

class ProductIaClassController extends Controller
{
    /**
     * Muestra tabla con todas las clases y marca las asignadas al producto.
     */
    public function index(Product $product)
    {
        /* --- IDs que ya están en el pivot --- */
        $activeIds = DB::table('products_datision_objects_ia_classes')
            ->where('products_id', $product->id)
            ->pluck('datision_objects_ia_classes_id')
            ->toArray();

        /* --- Todas las clases + campo boolean “selected” --- */
        $classes = DatisionObjectsIaClass::select('id', 'name')
            ->get()
            ->map(fn($c) => [
                'id'       => $c->id,
                'name'     => $c->name,
                'selected' => in_array($c->id, $activeIds),
            ]);

        return view('products.ia-classes-table', compact('product', 'classes'));
    }
}