@props( [ 'controller', 'data', 'video', 'video_fps', 'video_w', 'video_h', 'hotpointEditor', 'hotpoints', 'productos', 'related', 'txtrelated', 'keylist', 'ubp', 'datision', 'tabs', 'ia_selected_classes', 'ia_available_classes', 'ai_url', 'threshold_secs', 'ia_clases', 'objects' ] )

@php
    $title = ucwords($controller->getParams('plural')) . ': Edit ' . strtolower($controller->getParams('singular'));
@endphp

<x-layouts.app title="{{$title}}">
    @isset( $tabs )
        <input type="hidden" value="{{ $tabs }}" id="tabs" />
    @endisset
    <div class="tabs-container"></div>

     {{-- Aquí puedes llamar al método reset() si necesitas que el contador empiece desde 1 para esta sección --}}
    @php
        \App\Helpers\TabCounter::reset();
    @endphp

    @if ( isset( $objects ) )
        <x-layouts.tab-dashboard />
    @endif


@php
    if ( !isset( $related ) ) {
        $related = null;
    }
    if ( !isset( $txtrelated ) ) {
        $txtrelated = null;
    }
    if ( !isset( $video ) ) {
        $video = null;
    }
@endphp

    <x-layouts.tab-edit :controller="$controller" :data="$data" :related="$related" :txtrelated="$txtrelated" :video="$video" />

    @if ( isset( $objects ) )
        <x-layouts.tab-objects :objects="$objects"/>
    @endif

    @if ( isset( $hotpointEditor ) )
        <x-layouts.tab-hotpoint :data="$data" :hotpointEditor="$hotpointEditor" :productos="$productos" :hotpoints="$hotpoints" :video="$video" />
    @endif

    @php
        // <x-layouts.relatedcontainer :controller="$controller" :main="$main" :disabled="$disabled" />
        // <x-layouts.tags :tags="$tags" :controller="$controller" :vinculated="$vinculated" />
    @endphp

    @if ( isset( $keylist ) )
        <x-layouts.tab-keylist :data="$data" :keylist="$keylist" />
    @endif

    @if ( isset( $ubp ) )
        <x-layouts.tab-permisions :data="$data" :ubp="$ubp" />
    @endif

    @if ( isset( $datision ) )
        <x-layouts.tab-aiobjects :data="$data" :ai_url="$ai_url" :datision="$datision" :threshold_secs="$threshold_secs" :ia_clases="$ia_clases" :video="$video" :video_fps="$video_fps" :video_w="$video_w" :video_h="$video_h" />
    @endif

    @isset($ia_selected_classes)
        {!! view('partials.ia-classes-container', [
        'data' => $data,
        'ia_selected_classes' => $ia_selected_classes,
        'ia_available_classes' => $ia_available_classes
        ])->render() !!}
    @endisset

    <div class="cobertura" style="display:none;">
        <div class="iwt-spinner"></div>
    </div>
</x-layouts.app>