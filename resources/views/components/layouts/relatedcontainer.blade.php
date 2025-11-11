@props( [ 'controller', 'main', 'disabled' ] )

@isset ( $main )
	<div class="related-container">
		<div class="related-container-panel">
			<h3>{{ $controller->getText( 'left_column' ) }}</h3>
			@foreach ($main as $tag)
			<p class="content-button">
				<span class="text-button">{{ $tag->name }}</span>
				<a class="btn btn-primary" href="{{ $url }}?add={{$tag->id}}">
					{{$controller->getText( 'left_column_button' )}}
				</a>
			</p>
			@endforeach
		</div>
		<div class="related-container-panel">
			<h3>{{ $controller->getText( 'right_column' )}}</h3>
			@foreach ($disabled as $tag)
			@if ( '' != $tag->id )
			<p class="content-button">
				<span class="text-button">{{ $tag->name }}</span>
				<a class="btn btn-primary" href="{{ $url }}?remove={{$tag->id}}">
					{{ $controller->getText( 'right_column_button' )}}
				</a>
			</p>
			@endif
			@endforeach
		</div>
	</div>
@endisset