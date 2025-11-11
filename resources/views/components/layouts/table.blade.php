@props([
    'controller' => [],
	'datas' => [],
	'related' => [],
	'txtrelated' => '',
	'urlrelated' => '',
    'canEdit' => true,
    'canDelete' => true,
    'canCreate' => true,
    'actions' => [],
])
<?php

    function reemplazarCorchetes($input, $data) {
        // Expresión regular para encontrar patrones entre corchetes
        $pattern = '/\[(.*?)\]/';

        // Función de reemplazo
        $result = preg_replace_callback($pattern, function($matches) use ($data) {
            // Obtiene el contenido dentro de los corchetes
            $key = $matches[1];

            // Evalúa la expresión dentro de los corchetes
            // Nota: eval() ejecuta código PHP contenido en una cadena
            // Es importante validar y sanitizar las expresiones antes de usar eval() por razones de seguridad
            $evaluatedValue = null;
            eval('$evaluatedValue = ' . $key . ';');

            // Retorna el valor evaluado o una cadena vacía si es nulo
            return $evaluatedValue !== null ? $evaluatedValue : '';
        }, $input);

        return $result;
    }

    $tmp = [];
    if ( $canEdit ) {
        $tmp[] = [
            'name' => 'Edit', 
            'color' => 'primary', 
            'action' => $controller->getParams('view') . '.edit', 
            'url' => ''
        ];
    }

    if ( $canDelete ) {
        $tmp[] = [
            'name' => 'Delete', 
            'color' => 'danger', 
            'action' => '',
            'url' => '',
            'element' => 'button',
            'type' => 'submit'
        ];
    }

    $base = [
        'name' => 'no name',
        'color' => 'primary', 
        'action' => '',
        'url' => '',
        'element' => 'a',
        'type' => ''
    ];
    $actions = array_merge( $tmp, $actions );
    foreach( $actions as $k => $action ) {
        $actions[ $k ] = array_merge( $base, $action );
    }

    $title = ucwords( $controller->getParams( 'plural' ) );
?>

<x-layouts.app title="{{ $title }}">

    <div class="row">
        <div class="header">
            <div class="pull-left">
                <h2> {{ ucfirst($controller->getParams('plural')) }}</h2>
            </div>
            @if ( $canCreate )
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route($controller->getParams('view') . '.create') }}"> Create new
                    {{ $controller->getParams('singular') }}</a>
            </div>
            @endif
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif


    <table class="iwt-{{ $controller->getParams('plural') }} table table-bordered">
        <tr>
            @foreach ($controller->getParams('fields') as $field)
                @if ( isset( $field[ 'hide_on_index' ] ) && $field[ 'hide_on_index' ] )
                @else
                    <?php
                        $ao = '';
                        $ac = '';
                        if ( isset( $field[ 'orderby' ] ) ) {
                            $ao = '<a href="?orderby=' . $field[ 'name' ] . '&ordertype=';
                            if ( isset( $_GET[ 'ordertype' ] ) ) {
                                if ( isset( $_GET[ 'orderby' ] ) && $field[ 'name' ] == $_GET[ 'orderby' ] ) {
                                    if ( 'asc' == $_GET[ 'ordertype' ] ) {
                                        $ao .= 'desc';
                                    } else {
                                        $ao .= 'asc';
                                    }
                                } else {
                                    $ao .= 'asc';
                                }
                            } else {
                                $ao .= 'asc';
                            }
                            $ao .= '">';
                            $ac = '';
                            if ( isset( $_GET[ 'orderby' ] ) && $field[ 'name' ] == $_GET[ 'orderby' ] ) {
                                if ( isset( $_GET[ 'ordertype' ] ) ) {
                                    if ( 'asc' == $_GET[ 'ordertype' ] ) {
                                        $ac .= ' <i class="fa-solid fa-sort-up"></i>';
                                    } else {
                                        $ac .= ' <i class="fa-solid fa-sort-down"></i>';
                                    }
                                } else {
                                    if ( isset( $_GET[ 'orderby' ] ) ) {
                                        $ac .= ' <i class="fa-solid fa-sort-up"></i>';
                                    }
                                }
                            }
                            $ac .= '</a>';
                        }
                    ?>
                    <th><?php echo $ao; ?>{{ $field['label'] }}<?php echo $ac; ?></th>
                @endif
            @endforeach
            <th>Actions</th>
        </tr>
        @foreach ($datas as $data)
            <tr>
                @foreach ($controller->getParams('fields') as $field)
                    @foreach ($data->attributesToArray() as $k => $v)
                        @if ($k == $field['name'])
                            @if ( isset( $field[ 'hide_on_index' ] ) && $field[ 'hide_on_index' ] )
                            @else
                                <td class="listado">
                                    @php( $v2 = '-1' )
                                    @php( $class = '' )
                                    @if ( isset( $field[ 'format' ] ) && 'switch' == $field[ 'format' ] )
                                        @php( $v = ( $v == '1' ? 'Disabled' : 'Enabled' ) )
                                        @php( $class = strtolower( $v ) )
                                    @elseif ( isset( $field[ 'format' ] ) && 'related' == $field[ 'format' ] )
                                        @if ( isset( $related[ $v ] ) )
                                            @php( $v2 = $v )
                                            @php( $v = $related[ $v ][ 'name' ] )
                                            <a href="/{{ $urlrelated }}/{{ $related[ $v2 ][ 'id' ] }}/edit">                                            
                                        @else
                                            @if ( isset( $field['values'] ) && in_array( $v, $field['values'] ) )
                                            @else
                                                @php( $v = 'No ' . $txtrelated . ' selected' )
                                                @if ( '-' == $txtrelated )
                                                    @php( $v = '---' )
                                                @endif
                                                @if ( '' == $txtrelated )
                                                    @php( $v = '' )
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                    @if( isset( $field['force_nbsp']) && $field['force_nbsp'])
                                        @php( $v = str_replace( ' ', '&nbsp;', $v ) )
                                    @endif
                                    <?php
                                        $ao = '';
                                        $ac = '';
                                        if ( 'id' == $field[ 'name' ] || 'name' == $field[ 'name' ] ) {
                                            $ao = '<a href="' . $controller->getParams('plural') . '/' . $data->id . '/edit">';
                                            $ac = '</a>';
                                        }
                                    ?>
                                    <span class="{{$class}}">
                                        <?php echo $ao; ?>{!! $v !!}<?php echo $ac; ?>
                                    </span>
                                    @if ( isset( $field[ 'format' ] ) && 'related' == $field[ 'format' ] )
                                        @if ( isset( $related[ $v2 ] ) )
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            @endif
                        @endif
                    @endforeach
                @endforeach
                <td class="listado acciones">
                    <form action="{{ route($controller->getParams('view') . '.destroy', $data->id) }}" method="POST">
                        @foreach( $actions as $action )
                            <?php
                                $url = '';
                                if ( '' != $action[ 'url' ] ) {
                                    $url = reemplazarCorchetes( $action[ 'url' ], $data );
                                } else {
                                    if ( '' != $action[ 'action' ] ) {
                                        $url = route( $action[ 'action' ], $data->id );
                                    }
                                }
                            ?>
                            @if( 'a' == $action[ 'element' ] )
                                <a class="btn btn-{{ $action[ 'color' ] }}" href="{{ $url }}">{{ $action[ 'name' ] }}</a>
                            @endif
                            @if( 'button' == $action[ 'element' ] )
                                <button class="btn btn-{{ $action[ 'color' ] }}" type="{{ $action[ 'type' ] }}">{{ $action[ 'name' ] }}</button>
                            @endif
                        @endforeach

                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    @if(method_exists($datas, 'links'))
        {!! $datas->links() !!}
    @endif

</x-layouts.app>
