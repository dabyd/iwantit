<x-layouts.app title=": Add new {{ $controller->getParams('singular') }}">
    <div class="iwt-inform row">
        <div class="header">
            <div class="pull-left">
                <h2>Inform of products for {{ $controller->getParams('singular') }}</h2>
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

    <div class="iwt-inform row listado">
        <div class="linea header">
            <div class = "producto">Name</div>
            <div class = "imagen">Image</div>
            <div class = "marca">Brand</div>
            <div class = "logo">Logo</div>
            <div class = "veces">Hotpoints</div>
        </div>
        @foreach ($obj2 as $elemento)
            <div class="linea">
                <div class = "producto">
                    <a href="/products/{{ $elemento->pr_id }}/edit">{{ $elemento->pr_name }}</a>
                </div>
                <div class="imagen">
                    @if ( '' != $elemento->pr_image )
                        <img src="/uploads/{{$elemento->pr_image}}" />
                    @endif
                </div>
                <!-- <div class = "producto">
                    <a href="/products/{{ $elemento->pr_id }}/edit">{{ $elemento->pr_name }}</a>
                </div> -->
                <!-- <div class="logo">
                    @if ( '' != $elemento->br_logo )
                        <img src="/uploads/{{$elemento->br_logo}}" />
                    @endif
                </div> -->
                <div class = "marca">
                    @if ( '' == $elemento->br_name )
                        No brand assigned
                    @else
                        <a href="/brands/{{ $elemento->br_id }}/edit">{{ $elemento->br_name }}</a>
                    @endif
                </div>
                <div class="logo">
                    @if ( '' != $elemento->br_logo )
                        <img src="/uploads/{{$elemento->br_logo}}" />
                    @endif
                </div>
                <div class = "veces">
                    {{ $elemento->veces }}
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
