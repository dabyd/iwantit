<?php

namespace App\View\Components\Layouts;

use App\Helpers\TabCounter; // Importa tu clase de contador
use Illuminate\View\Component;

class TabDashboard extends Component
{
    public int $currentCount;

    public function __construct() {
        $this->currentCount = TabCounter::incrementAndGet(); // Usa el servicio de contador
    }

    public function render()
    {
        return view('components.layouts.tab-dashboard');
    }
}