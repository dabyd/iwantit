<?php
    $tmp = 'brands';
    if ( isset( $txtrelated ) ) {
        $tmp = $txtrelated;
    }
    $txtrelated = $tmp;
?>
<x-layouts.app title=": Add new {{ $controller->getParams('singular') }}">
    <div class="row">
        <div class="header">
            <div class="pull-left">
                <h2>Create new {{ $controller->getParams('singular') }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route($controller->getParams('view') . '.index') }}"> Back</a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route($controller->getParams('view') . '.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row ficha">
            @foreach ($controller->getParams('fields') as $field)
                @if ($field['editable'])
                    <div class="elemento">
                        @if ( 'text' == $field[ 'type' ] || 'email' == $field[ 'type' ] )
                            <div class="form-group">
                                <strong>{{ $field[ 'label' ] }}:</strong>
                                <input type="{{ $field[ 'type' ] }}" name="{{ $field[ 'name' ] }}" class="form-control" placeholder="{{ $field[ 'label' ] }}">
                            </div>
                        @elseif( 'select' == $field[ 'type' ] )
                            <div class="form-group">
                                <strong>{{ $field[ 'label' ] }}:</strong>
                                <select class="form-control" name="{{ $field[ 'name' ] }}">
                                    @if ( isset( $field[ 'format' ] ) && 'switch' == $field[ 'format' ] )
                                        <option class="form-control" value="0">Enabled</option>
                                        <option class="form-control" value="1">Disabled</option>
                                    @elseif ( isset( $field[ 'format' ] ) && 'related' == $field[ 'format' ] && isset( $field[ 'values' ] ) )
                                        @foreach ( $field[ 'values' ] as $element )
                                            <option class="form-control" value="{{$element}}">{{$element}}</option>
                                        @endforeach
                                    @elseif ( isset( $field[ 'format' ] ) && 'related' == $field[ 'format' ] )
                                        <option class="form-control" value="0" >No {{$txtrelated}} selected</option>
                                        @foreach ( $related as $element )
                                            <option class="form-control" value="{{$element[ 'id' ]}}">{{ $element[ 'name' ]}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @elseif ( 'file' == $field[ 'type' ] )
                            <div class="form-group">
                                <strong>{{ $field[ 'label' ] }}:</strong>
                                <input type="{{ $field[ 'type' ] }}" name="{{ $field[ 'name' ] }}" class="form-control" placeholder="{{ $field[ 'label' ] }}">
                            </div>
                        @endif
<!--
                        <div class="form-group">
                            @if ( isset( $field[ 'format' ] ) && 'switch' == $field[ 'format' ] )
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="{{ $field['name'] }}">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">Disabled</label>
                                </div>
                            @else
                                <strong>{{ $field['label'] }}:</strong>
                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" class="form-control"
                                    placeholder="{{ $field['label'] }}">
                            @endif
                        </div>
-->
                    </div>
                @endif
            @endforeach
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button id="submit_create" type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </form>
    <div class="cobertura" style="display:none;">
        <div class="iwt-spinner"></div>
    </div>
</x-layouts.app>
