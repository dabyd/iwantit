<x-layouts.app title="View {{ $controller->getParams('singular') }}">
    <div class="row">
        <div class="header">
            <div class="pull-left">
                <h2> Show {{$controller->getParams('singular')}}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route( $controller->getParams('view') . '.index') }}"> Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                @foreach( $controller->getParams('fields') as $field )
                    @foreach( $data->attributesToArray() as $k => $v )
                        @if ( $k == $field[ 'name' ] )
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>{{ ucfirst( $k ) }}: </strong>
                                    {{ $v }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
