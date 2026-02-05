<?php

namespace App\View\Components\Layouts;

use App\Models\Project;
use App\Models\ClickStatistic;
use App\Models\Hotpoint;
use App\Models\HotpointsDate;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\View\Component;

class TabDashboard extends Component
{
    public int $currentCount;
    public Project $project;
    public array $stats = [];
    public array $chartData = [];
    public array $projectInfo = [];
    public array $brandRanking = [];

    public function __construct(Project $project) {
        $this->project = $project;
        $this->currentCount = \App\Helpers\TabCounter::incrementAndGet();
        $this->calculateStats();
    }

    private function calculateStats() {
        $vid = $this->project->id;

        // 1. Estadísticas básicas de ClickStatistic
        $this->stats['totalViews'] = ClickStatistic::where('versions_id', $vid)->where('type', 'view')->count();
        $this->stats['totalProductViews'] = ClickStatistic::where('versions_id', $vid)->where('type', 'view_p')->count();
        $this->stats['totalClicks'] = ClickStatistic::where('versions_id', $vid)->where('type', 'click')->count();
        $this->stats['productClicks'] = ClickStatistic::where('versions_id', $vid)->where('type', 'click')->whereNotNull('products_id')->count();
        $this->stats['brandClicks'] = ClickStatistic::where('versions_id', $vid)->where('type', 'click')->whereNotNull('brands_id')->count();
        
        $this->stats['conversionRate'] = $this->stats['totalViews'] > 0 
            ? round(($this->stats['totalClicks'] / $this->stats['totalViews']) * 100, 1) 
            : 0;

        // 2. Información del Proyecto (Productos, Marcas, Revenue)
        $productsInProject = DB::table('hotpoints')
            ->where('versions_id', $vid)
            ->distinct()
            ->pluck('products_id');

        $this->projectInfo['productsCount'] = $productsInProject->count();
        
        $this->projectInfo['brandsCount'] = DB::table('products')
            ->whereIn('id', $productsInProject)
            ->distinct()
            ->pluck('brands_id')
            ->count();

        // Calcular Revenue real y % productos habilitados
        $totalRevenue = 0;
        $enabledProducts = 0;
        $brandStats = [];

        // Obtener tiempos de visualización agrupados por producto
        $groupedHotpoints = Hotpoint::getGroupedHotpoints($vid);
        
        // Cargar todos los productos del proyecto con sus marcas de una vez
        $products = Product::with('brand')->whereIn('id', $productsInProject)->get()->keyBy('id');

        foreach ($groupedHotpoints as $data) {
            $productId = $data['products_id'];
            $veces = $data['time_groups'];
            
            $ttime = 0;
            foreach ($veces as $group) {
                $last = $group->last();
                $first = $group->first();
                if ($last && $first) {
                    $ttime += ($last->time - $first->time);
                }
            }

            $hpd = HotpointsDate::where('project_id', $vid)->where('product_id', $productId)->first();
            $productRevenue = 0;
            $isEnabled = false;

            if ($hpd) {
                $productRevenue = ($hpd->price ?? 0) * ceil($ttime);
                $totalRevenue += $productRevenue;
                if ($hpd->estado) {
                    $enabledProducts++;
                    $isEnabled = true;
                }
            }

            // Para el ranking de marcas
            $product = $products->get($productId);
            if ($product && $product->brand) {
                $brandId = $product->brand->id;
                if (!isset($brandStats[$brandId])) {
                    $brandStats[$brandId] = [
                        'id' => $brandId,
                        'name' => $product->brand->name,
                        'logo' => $product->brand->filename,
                        'revenue' => 0,
                        'totalProducts' => 0,
                        'enabledProducts' => 0,
                        'status' => 'Activo'
                    ];
                }
                $brandStats[$brandId]['revenue'] += $productRevenue;
                $brandStats[$brandId]['totalProducts']++;
                if ($isEnabled) {
                    $brandStats[$brandId]['enabledProducts']++;
                }
            }
        }


        $this->projectInfo['totalRevenue'] = $totalRevenue;
        $this->projectInfo['productsEnabledPercent'] = $this->projectInfo['productsCount'] > 0
            ? round(($enabledProducts / $this->projectInfo['productsCount']) * 100)
            : 0;

        // Preparar Ranking de Marcas
        usort($brandStats, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        $this->brandRanking = array_slice($brandStats, 0, 10);

        // 3. Datos del Gráfico (Últimos 14 días)
        $days = [];
        $viewData = [];
        $clickData = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $days[] = Carbon::now()->subDays($i)->format('d/m');
            
            $viewData[] = ClickStatistic::where('versions_id', $vid)
                ->where('type', 'view')
                ->whereDate('created_at', $date)
                ->count();
                
            $clickData[] = ClickStatistic::where('versions_id', $vid)
                ->where('type', 'click')
                ->whereDate('created_at', $date)
                ->count();
        }

        $this->chartData = [
            'labels' => $days,
            'views' => $viewData,
            'clicks' => $clickData,
        ];
    }

    public function render()
    {
        return view('components.layouts.tab-dashboard');
    }
}