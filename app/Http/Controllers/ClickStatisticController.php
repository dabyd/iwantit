<?php

namespace App\Http\Controllers;

use App\Models\ClickStatistic;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClickStatisticController extends Controller
{
    /**
     * Redirige al destino final tras registrar el clic en estadísticas.
     * 
     * GET /track/{type}/{id}?vid=X&time=Y
     * 
     * @param Request $request
     * @param string $type 'product' o 'brand'
     * @param int $id ID del producto o marca
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(Request $request, string $type, int $id)
    {
        $url = null;
        $versionsId = $request->query('vid');

        if ($type === 'product') {
            $product = Product::find($id);
            if ($product) {
                $url = $product->url;
            }
        } elseif ($type === 'brand') {
            $brand = Brand::find($id);
            if ($brand) {
                $url = $brand->url;
            }
        }

        // Si no se encuentra la URL, redirigir a home
        if (empty($url)) {
            return redirect('/');
        }

        // Registrar el clic en estadísticas
        try {
            ClickStatistic::logClick($request, $type, $id, $versionsId);
        } catch (\Exception $e) {
            // Log error but don't fail the redirect
            \Log::error('Error logging click statistic: ' . $e->getMessage());
        }

        // Redirigir al destino final
        return redirect()->away($url);
    }
}
