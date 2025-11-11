<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection; // Importa Collection

class Hotpoint extends Model
{
    use HasFactory;
    protected $fillable = ['versions_id', 'products_id', 'time', 'pos_x', 'pos_y'];

    /**
     * Obtiene hotpoints agrupados por tiempo sensible a la distancia.
     * Si $productId es null, agrupa primero por products_id y luego aplica la lógica de tiempo.
     *
     * @param int $versionId ID de la versión.
     * @param int|null $productId ID del producto (opcional).
     * @param float $timeDistance Umbral de tiempo para agrupar (en segundos). Por defecto 0.5.
     * @return Collection Un array de grupos.
     */
    public static function getGroupedHotpoints( $versionId, ?int $productId = null, float $timeDistance = 0.5): Collection
    {
        // 1. Construir la consulta base
        $query = self::where('versions_id', $versionId);

        // 2. Aplicar el filtro por products_id si se proporciona
        if ($productId !== null) {
            $query->where('products_id', $productId);
        }

        // 3. Obtener los hotpoints ordenados correctamente
        // Si agrupamos por producto, primero ordenamos por producto, luego por tiempo.
        // Si no, solo por tiempo.
        if ($productId === null) {
            $hotpoints = $query->orderBy('products_id')->orderBy('time')->get(['products_id', 'time', 'pos_x', 'pos_y']);
        } else {
            $hotpoints = $query->orderBy('time')->get(['products_id', 'time', 'pos_x', 'pos_y']);
        }

        if ($hotpoints->isEmpty()) {
            return collect(); // Retorna una colección vacía si no hay hotpoints
        }

        // Si $productId es nulo, primero agrupamos por products_id
        if ($productId === null) {
            return self::groupHotpointsByProductAndThenByTime($hotpoints, $timeDistance);
        } else {
            // Si $productId está definido, aplicamos directamente la lógica de agrupación por tiempo
            return self::groupHotpointsByTime($hotpoints, $timeDistance);
        }
    }


    /**
     * Lógica de agrupación de hotpoints sensible a la distancia en el tiempo.
     *
     * @param Collection $hotpoints Colección de hotpoints ya filtrados y ordenados.
     * @param float $timeDistance Umbral de tiempo para agrupar.
     * @return Collection Un array de grupos de hotpoints.
     */
    protected static function groupHotpointsByTime(Collection $hotpoints, float $timeDistance): Collection
    {
        $groupedHotpoints = collect();
        $currentGroup = collect();
        $lastTime = null;

        foreach ($hotpoints as $hotpoint) {
            if ($lastTime === null || ($hotpoint->time - $lastTime) > $timeDistance) {
                // Si es el primer registro o la diferencia excede el umbral,
                // cerramos el grupo actual (si no está vacío) y abrimos uno nuevo.
                if ($currentGroup->isNotEmpty()) {
                    $groupedHotpoints->push($currentGroup);
                }
                $currentGroup = collect();
            }
            $currentGroup->push($hotpoint);
            $lastTime = $hotpoint->time;
        }

        // Asegúrate de añadir el último grupo si no está vacío
        if ($currentGroup->isNotEmpty()) {
            $groupedHotpoints->push($currentGroup);
        }

        return $groupedHotpoints;
    }


    /**
     * Agrupa hotpoints primero por products_id y luego aplica la lógica de tiempo dentro de cada producto.
     *
     * @param Collection $hotpoints Colección de hotpoints ya filtrados y ordenados por product_id y time.
     * @param float $timeDistance Umbral de tiempo para agrupar.
     * @return Collection Una colección de arrays, donde cada array representa los grupos de un producto.
     */
    protected static function groupHotpointsByProductAndThenByTime(Collection $hotpoints, float $timeDistance): Collection
    {
        // Primero agrupamos por products_id usando el método groupBy de Collection
        $groupedByProduct = $hotpoints->groupBy('products_id');

        $finalGroupedOutput = collect();

        foreach ($groupedByProduct as $productIdKey => $productHotpoints) {
            // Para cada grupo de producto, aplicamos nuestra lógica de agrupación por tiempo
            $timeBasedGroupsForProduct = self::groupHotpointsByTime($productHotpoints, $timeDistance);

            if ($timeBasedGroupsForProduct->isNotEmpty()) {
                $finalGroupedOutput->push([
                    'products_id' => $productIdKey,
                    'time_groups' => $timeBasedGroupsForProduct
                ]);
            }
        }

        return $finalGroupedOutput;
    }
}