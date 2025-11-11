@props( [ 'tags', 'controller', 'vinculated' ] )

@isset ( $tags )
    <div class="related-container">
        <div class="related-container-panel">
            <h3>{{ $controller->getText( 'left_column' ) }}</h3>
            @foreach ($tags as $tag)
				<p class="content-button">
					<span class="text-button">{{ $tag->name }}</span>
					<a class="btn btn-primary" href="{{ $url }}?add={{$tag->id}}">
						{{ $controller->getText( 'left_column_button' ) }}
					</a>
				</p>
            @endforeach
        </div>
        <div class="related-container-panel">
            <h3>{{ $controller->getText( 'right_column' ) }}</h3>
            @foreach ($vinculated as $tag)
				<p class="content-button">
					<span class="text-button">{{ $tag->name }}</span>
					<span class="grupo-botones">
						<a class="btn btn-primary style-{{$tag->disabled}}" href="{{ $url }}?change_status={{$tag->id}}&status={{$tag->disabled}}">
							<?php echo ($tag->disabled == '1' ? 'Disabled <span>Enable it</span>' : 'Enabled <span>Disable it</span>'); ?>
						</a>
						<br>
						<a class="btn btn-primary" href="{{ $url }}?remove={{$tag->id}}">
							{{ $controller->getText( 'right_column_button' ) }}
						</a>
					</span>
				</p>
            @endforeach
        </div>
    </div>
@endisset