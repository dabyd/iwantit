<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\DatisionObjectsIaClass;
use Illuminate\Support\Facades\DB;

class ProductIaClassAjax extends Controller
{
    public function update(Request $request, Product $product)
    {
        $action = $request->string('action');          // add  | remove
        $ids    = $request->input('ids', []);          // array de enteros

/*
		echo '<pre>';
		print_r( $action );
		print_r( $ids );
		print_r( $product );
		echo '</pre>';
*/

/*
        if ($action === 'add') {
            $product->iaClasses()->syncWithoutDetaching($ids);
        } elseif ($action === 'remove') {
            $product->iaClasses()->detach($ids);
        }
*/

		/**
		 *  $action   Illuminate\Support\Stringable  ->  "add"  |  "remove"
		 *  $ids      array de IDs (ej. [248,206,318])
		 *  $product  App\Models\Product  (ya cargado)
		 */

		$action = (string) $action;          // casteamos el Stringable a texto
		$ids    = array_map('intval', $ids); // sanitizamos

		/* ---------- INSERT (add) ---------- */
		if ($action === 'add') {

			// construimos los valores a insertar: uno por cada ID
			$now  = now();
			$rows = array_map(fn($id) => [
				'products_id'                    => $product->id,
				'datision_objects_ia_classes_id' => $id,
				'created_at'                     => $now,
				'updated_at'                     => $now,
			], $ids);

			// INSERT IGNORE para evitar duplicados
			DB::table('products_datision_objects_ia_classes')
				->insertOrIgnore($rows);       // Laravel >=8

		/* ---------- DELETE (remove) ---------- */
		} elseif ($action === 'remove') {

			DB::table('products_datision_objects_ia_classes')
				->where('products_id', $product->id)
				->whereIn('datision_objects_ia_classes_id', $ids)
				->delete();

		} else {
			abort(400, 'Acción no reconocida');
		}

        // --- reconstruir arrays para la vista parcial ---
        $activeIds = $product->iaClasses()->pluck('datision_objects_ia_classes.id as id')->toArray();

        $ia_selected_classes  = DatisionObjectsIaClass::select('id', 'name')
                                ->whereIn('id', $activeIds)
                                ->orderBy('name')
                                ->get()
                                ->toArray();

        $ia_available_classes = DatisionObjectsIaClass::select('id', 'name')
                                ->whereNotIn('id', $activeIds)
                                ->orderBy('name')
                                ->get()
                                ->toArray();

        return view('partials.ia-classes-container', [
            'data'                  => $product,
            'ia_selected_classes'   => $ia_selected_classes,
            'ia_available_classes'  => $ia_available_classes,
        ])->render();                    // se devuelve HTML
    }
}