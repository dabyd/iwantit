<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatisionParameterRequest;
use App\Models\DatisionParameter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DatisionParameterController extends Controller
{
    // Si quieres proteger: $this->middleware('auth'); en __construct()

    public function index(): View
    {
        $items = DatisionParameter::latest()->paginate(15);
        $controller = $this;
        return view('datision_parameters.index', compact('items','controller'));
//        return view('datision_parameters.index', compact('items'));
    }

    public function create(): View
    {
        return view('datision_parameters.create');
    }

    public function store(DatisionParameterRequest $request): RedirectResponse
    {
        DatisionParameter::create($request->validated());
        return redirect()
            ->route('datision-parameters.index')
            ->with('success', __('datision.flash.created'));
    }

    public function show(DatisionParameter $datision_parameter): View
    {
        return view('datision_parameters.show', ['item' => $datision_parameter]);
    }

    public function edit(DatisionParameter $datision_parameter): View
    {
        $controller = $this;
        return view('datision_parameters.edit', compact('datision_parameter','controller'));
//        return view('datision_parameters.edit', ['item' => $datision_parameter]);
    }

    public function update(DatisionParameterRequest $request, DatisionParameter $datision_parameter): RedirectResponse
    {
        $datision_parameter->update($request->validated());
        return redirect()
            ->route('datision-parameters.index')
            ->with('success', __('datision.flash.updated'));
    }

    public function destroy(DatisionParameter $datision_parameter): RedirectResponse
    {
        $datision_parameter->delete();
        return redirect()
            ->route('datision-parameters.index')
            ->with('success', __('datision.flash.deleted'));
    }

    /**
     * Devuelve el valor de un campo de DatisionParameter
     * 
     * @param string $field Nombre de la columna en la tabla
     * @return mixed|null   Valor del campo o null si no existe
     */
    public static function getValue(string $field)
    {
        $record = DatisionParameter::query()->first();

        if (!$record) {
            return null; // no hay registros en la tabla
        }

        if (!array_key_exists($field, $record->getAttributes())) {
            return null; // el campo no existe en el modelo
        }

        return $record->$field;
    }

    public function getParams( $data = '' ) {
        $params = [];
        $params[ 'view' ] = 'datision-parameters';
        $params[ 'singular' ] = 'Datision pamareter';
        $params[ 'plural' ] = 'Datision pamareters';
        $params[ 'fields' ] = [
            [
                'label' => 'ID',
                'name' => 'id',
                'editable' => false,
                'orderby' => false
            ],
            [
                'label' => 'URL',
                'name' => 'machine_url',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
            [
                'label' => 'Threshold Secs',
                'name' => 'threshold_sec',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
            [
                'label' => 'Default frames tolerance',
                'name' => 'frames',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
            [
                'label' => 'Default X-axis tolerance',
                'name' => 'x1',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
            [
                'label' => 'Default Y-axis tolerance',
                'name' => 'y1',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
            [
                'label' => 'Price per second low',
                'name' => 'low_price',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
            [
                'label' => 'Price per second medium',
                'name' => 'medium_price',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
            [
                'label' => 'Price per second high',
                'name' => 'high_price',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
            [
                'label' => 'Price per second extra',
                'name' => 'extra_price',
                'editable' => true,
                'type' => 'text',
                'orderby' => false
            ],
        ];
        $ret = $params;
        if ( '' != $data && isset( $params[ $data ] ) ) {
            $ret = $params[ $data ];
        }
        return $ret;
    }
}