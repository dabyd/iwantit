<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\ClickStatistic;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClickStatisticController extends Controller
{
    /**
     * Redirige al destino final tras registrar el clic en estadísticas.
     *
     * GET /track/{type}/{id}?vid=X&time=Y
     *
     * @param  string  $type  'product' o 'brand'
     * @param  int  $id  ID del producto o marca
     * @return RedirectResponse
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
            Log::error('Error logging click statistic: '.$e->getMessage());
        }

        // Redirigir al destino final
        return redirect()->away($url);
    }

    /**
     * Rastrea la visualización de la imagen del producto y la devuelve.
     * Si no existe, devuelve un píxel blanco de 1x1.
     *
     * GET /track/image/{id}?vid=X
     *
     * @param  int  $id  ID del producto
     * @return BinaryFileResponse|Response
     */
    public function showProductImage(Request $request, int $id)
    {
        $versionsId = $request->query('vid');
        $product = Product::find($id);

        // Registrar la visualización en estadísticas
        try {
            ClickStatistic::logProductView($request, $id, $versionsId);
        } catch (\Exception $e) {
            Log::error('Error logging product image view statistic: '.$e->getMessage());
        }

        // Devolver la imagen si existe
        if ($product && $product->filename) {
            $path = public_path('uploads/'.$product->filename);
            if (file_exists($path)) {
                return response()->file($path);
            }
        }

        // Devolver píxel blanco 1x1 si no hay imagen (GIF)
        $pixel = base64_decode('R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=');

        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
