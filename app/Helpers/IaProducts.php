<?php
//  app/Helpers/IaProducts.php   (o en el lugar que prefieras)

namespace App\Helpers;

use App\Models\Product;
use App\Models\DatisionObjectsIaClass;
use Illuminate\Support\Facades\DB;

class IaProducts
{
    /**
     * Devuelve todos los productos que poseen la clase IA indicada.
     *
     * @param  string  $className  Nombre (o parte del nombre) de la clase IA, ej. "cup"
     * @return array   Formato: [ ['id' => 14, 'name' => 'Pamela France Fucsia'], … ]
     */
    public static function byIaClass(string $className): array
    {
		// 1) localizar la clase IA por coincidencia exacta
		$iaClass = DatisionObjectsIaClass::where('name', $className)->first();
/*
        // 1) localizar la clase IA (case-insensitive, primer match)
        $iaClass = DatisionObjectsIaClass::whereRaw('LOWER(name) LIKE ?', [ '%' . strtolower($className) . '%' ])
                    ->first();
*/

        if (!$iaClass) {
            return [];                         // no existe la clase → lista vacía
        }

        // 2) obtener los productos vinculados a ese ID en la tabla pivote
        return Product::select('products.id', 'products.name')
            ->join('products_datision_objects_ia_classes as piv', 'products.id', '=', 'piv.products_id')
            ->where('piv.datision_objects_ia_classes_id', $iaClass->id)
            ->orderBy('products.name')
            ->get()
            ->toArray();                       // → array listo para usar en controladores / vistas
    }
}