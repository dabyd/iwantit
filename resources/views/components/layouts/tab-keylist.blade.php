@props( [ 'data', 'keylist' ] )

@php
$currentCount = \App\Helpers\TabCounter::incrementAndGet();
@endphp

@isset( $keylist )
	<div class="tab-{{ $currentCount }}">
		<h2>Key File list</h2>
		<h3>({{ $data->name }})</h3>
		@foreach ($keylist as $file)
		<div class="licenses-list">
			<input type="text" value="{{$file->name}}" placeholder="Unnamed key file" id="keyfile_name" data-id="{{$file->id}}" />
			@if ( '0' == $file->disabled )
			<button class="btn btn-primary _enabled" id="enable_disable_lic" data-id="{{$file->id}}">Enabled</button>
			@else
			<button class="btn btn-primary _disabled" id="enable_disable_lic" data-id="{{$file->id}}">Disabled</button>
			@endif
			<a href="/keyfile/{{$file->fn}}" id="df-{{$file->id}}" class="btn btn-primary" download="">Download keyfile</a>
			<button class="btn btn-primary" id="delete_keyfile" data-id="{{$file->id}}">Delete keyfile</button>
		</div>
		@endforeach
		<button type="button" class="btn btn-primary" id="create_keyfile">Create new key file</button>
	</div>
@endisset