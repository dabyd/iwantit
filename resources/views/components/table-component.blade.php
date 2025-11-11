@props([
    'message' => null,
    'data' => [],
    'controller' => [],
    'fields' => [],
    'actions' => [],
    'createUrl' => '',
    'createText' => 'Create New'
])

<?php
    function getValueByName( $controller, $name ) {
        $ret = '';
        foreach( $controller->getParams('fields') as $field ) {
            if ( $field[ 'name' ] == $name ) {}
        }
    }
?>

<x-layouts.app title="Lista de Datos">
    <div class="row">
        <div class="header">
            <div class="pull-left">
                <h2>{{ ucfirst($controller->getParams('plural')) }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route($controller->getParams('view') . '.create') }}"> Create new
                    {{ $controller->getParams('singular') }}</a>
            </div>
        </div>
    </div>

    @if ($message)
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <!-- Incluir los archivos de JSTable -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jstable/dist/jstable.css">
    <script src="https://cdn.jsdelivr.net/npm/jstable"></script>

    <table id="datatable" class="iwt-{{ $controller->getParams('plural') }} table table-bordered">
        <thead>
            <tr>
                @foreach ($fields as $field)
                    <th data-sortable="true">{{ $field['label'] }}</th>
                @endforeach
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    @foreach ($fields as $field)
                        <td>
                            @if(!empty($field['nbsp']) && $field['nbsp'] === true)
                                {!! str_replace(' ', '&nbsp;', $row[$field['name']] ?? 'N/A') !!}
                            @else
                                {{ $row[$field['name']] ?? 'N/A' }}
                            @endif
                        </td>
                    @endforeach
                    <td class="acciones">
                        @foreach ($actions as $action)
                            <a href="{{ str_replace('{id}', $row['id'], $action['url']) }}" 
                               class="btn btn-{{ $action['color'] }}">
                                {{ $action['name'] }}
                            </a>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            new JSTable("#datatable", {
                searchable: true,
                sortable: true,
                perPage: 10,
            });
        });
    </script>

</x-layouts.app>