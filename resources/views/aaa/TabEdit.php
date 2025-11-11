<?php

namespace App\View\Components\Layouts;

use App\Helpers\TabCounter; // Asegúrate de que esta ruta sea correcta
use Illuminate\View\Component;

class TabEdit extends Component
{
    // Propiedad para el número de pestaña, generado por TabCounter
    public int $currentCount;

    // Propiedades públicas para recibir los datos de Blade
    public $controller;
    public $data;
    public $related;
    public $txtrelated;
    public $video;

    /**
     * Crea una nueva instancia del componente.
     * Los parámetros del constructor deben coincidir con los atributos pasados desde Blade.
     * Se les asignan valores por defecto (null) para hacerlos opcionales.
     *
     * @param mixed|null $controller Valor para el controlador.
     * @param mixed|null $data Datos adicionales.
     * @param mixed|null $related Elementos relacionados.
     * @param mixed|null $txtrelated Texto relacionado.
     * @param mixed|null $video URL o ID de video.
     * @return void
     */
    public function __construct(
        $controller = null,
        $data = null,
        $related = null,
        $txtrelated = null,
        $video = null
    ) {
        // Incrementa el contador global y asigna el valor a esta instancia del componente
        $this->currentCount = TabCounter::incrementAndGet();

        // Asigna los valores pasados al constructor a las propiedades públicas de la clase
        $this->controller = $controller;
        $this->data = $data;
        $this->related = $related;
        $this->txtrelated = $txtrelated;
        $this->video = $video;
    }

    /**
     * Obtiene la vista/contenido que representa el componente.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // Retorna la vista asociada al componente.
        // Todas las propiedades públicas ($currentCount, $controller, $data, etc.)
        // estarán automáticamente disponibles en esta vista.
        return view('components.layouts.tab-edit');
    }
}