@props( [ 'controller', 'data', 'related', 'txtrelated', 'video', 'readonly' ] )
@php
	$currentCount = \App\Helpers\TabCounter::incrementAndGet();
@endphp

<div class="tab-{{ $currentCount }}">	
	<div class="row">
		<div class="header">
			<div class="pull-left">
				<h2>Info</h2>
			</div>
			<div class="pull-right">
				<a class="btn btn-primary" href="{{ route( $controller->getParams( 'view' ) . '.index') }}"> Back</a>
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

	<form action="{{ route( $controller->getParams( 'view' ) . '.update', $data->id) }}" method="POST" enctype="multipart/form-data">
		@csrf
		@method('PUT')
		<input type="hidden" name="field_to_delete" value="" />
		<div class="row ficha">
			@foreach( $controller->getParams( 'fields' ) as $field )
				@foreach( $data->attributesToArray() as $k => $v )
					<?php
					$editable = true;
					if ($k == $field['name']  && $field['editable']) {
						$editable = true;
					} else {
						$editable = false;
					}
					if (isset($field['show_when'])) {
						$tmp = $data->attributesToArray();
						if ($tmp[$field['show_when']['field']] != $field['show_when']['value']) {
							$editable = false;
						}
					}
					$inputReadonly = isset($readonly) && $readonly ? 'readonly' : '';
					$selectDisabled = isset($readonly) && $readonly ? 'disabled' : '';
					?>
					@if ( $editable )
						<div class="iwt-{{ $field[ 'type' ] }}-field elemento">
							@if ( 'text' == $field[ 'type' ] )
								<div class="form-group">
									<strong>{{ $field[ 'label' ] }}:</strong>
									<input type="{{ $field[ 'type' ] }}" name="{{ $field[ 'name' ] }}" value="{{ $v }}" class="form-control" placeholder="{{ $field[ 'label' ] }}" {{ $inputReadonly }}>
								</div>
							@elseif( 'select' == $field[ 'type' ] )
								<?php
								$elements = $related;
								$default_option = true;
								if (isset($field['values'])) {
									if (!is_array($field['values'][0])) {
										$tmp = [];
										foreach ($field['values'] as $k) {
											$tmp[] = ['id' => $k, 'name' => $k];
										}
										$field['values'] = $tmp;
									}
									$elements = $field['values'];
									$default_option = false;
								}
								?>
								<div class="form-group">
									<strong>{{ $field[ 'label' ] }}:</strong>
									<select class="form-control" name="{{ $field[ 'name' ] }}" {{ $selectDisabled }}>
										@if ( isset( $field[ 'format' ] ) && 'switch' == $field[ 'format' ] )
											<option class="form-control" value="1" <?php echo ($v == '1' ? 'selected' : ''); ?>>Disabled</option>
											<option class="form-control" value="0" <?php echo ($v == '0' ? 'selected' : ''); ?>>Enabled</option>
										@elseif ( isset( $field[ 'format' ] ) && 'related' == $field[ 'format' ] )
											@php
												$sinmarca = true;
											@endphp
											@foreach ( $elements as $element )
												@if ($v == $element[ 'id' ])
													@php
														$sinmarca = false;
													@endphp
												@endif
												<option class="form-control" value="{{$element[ 'id' ]}}" <?php echo ($v == $element['id'] ? 'selected' : ''); ?>>{{ $element[ 'name' ]}}</option>
											@endforeach
											@if ( $default_option )
												<option class="form-control" value="0" <?php echo ($sinmarca ? 'selected' : ''); ?>>No {{$txtrelated}} selected</option>
											@endif
										@endif
									</select>
								</div>
							@elseif ( 'file' == $field[ 'type' ] )
								<input type="hidden" name="old_video" value="{{ $v }}" />
								<div class="form-group">
									<?php
									$c1 = 'style="display: none;"';
									$c2 = '';
									if ('' != $v || '' != $video) {
										$c2 = 'style="display: none;"';
										$c1 = '';
									}
									?>
									@if(!isset($readonly) || !$readonly)
										<button type="button" class="form-control show_file_button btn btn-warning" <?php echo $c1; ?>>Change the video</button>
									@endif
									<strong <?php echo $c2; ?>>{{ $field[ 'label' ] }}:</strong>
									<input type="{{ $field[ 'type' ] }}" name="{{ $field[ 'name' ] }}" class="form-control" placeholder="{{ $field[ 'label' ] }}" <?php echo $c2; ?> {{ $inputReadonly }}>
								</div>
							@elseif ( 'image' == $field[ 'type' ] )
								<input type="hidden" name="old_img" value="{{ $v }}" />
								<div class="form-group">
									<?php
									$c1 = 'style="display: none;"';
									$c2 = '';
									if ('' != $v) {
										$c2 = 'style="display: none;"';
										$c1 = '';
									}
									$txt = 'Change the image';
									if (isset($field['txt_button'])) {
										$txt = $field['txt_button'];
									}
									?>
									<input type="hidden" name="field_name" value="{{ $field[ 'name' ] }}" />
									@if(!isset($readonly) || !$readonly)
										<button type="button" class="form-control show_file_button btn btn-warning" <?php echo $c1; ?>>{{ $txt }}</button>
										<button type="button" class="form-control remove_image_button btn btn-danger" <?php echo $c1; ?>><?php echo str_replace('Change', 'Delete', $txt); ?></button>
									@endif
									<strong <?php echo $c2; ?>>{{ $field[ 'label' ] }}:</strong>
									<input type="file" name="{{ $field[ 'name' ] }}" class="form-control" placeholder="{{ $field[ 'label' ] }}" <?php echo $c2; ?>>
								</div>
								@if ( '' != $v )
									<?php
									$ec = '';
									if (isset($field['extra_class'])) {
										$ec = ' class="' . $field['extra_class'] . '" ';
									}
									?>
									<img src=" {{ URL::asset('uploads/' . $v ) }}" <?php echo $ec; ?> />
								@endif
							@elseif ( 'textarea' == $field[ 'type' ] )
								<div class="form-group">
									<strong>{{ $field[ 'label' ] }}:</strong>
									<textarea name="{{ $field[ 'name' ] }}" class="form-control" placeholder="{{ $field[ 'label' ] }}" {{ $inputReadonly }}>
									{{$v}}
									</textarea>
								</div>
							@endif
						</div>
					@endif
				@endforeach
			@endforeach
			<div class="col-xs-12 col-sm-12 col-md-12 text-center">
				@if(isset($readonly) && $readonly)
					<div class="alert alert-info mb-0"><i class="fa-solid fa-lock"></i> Read-only mode — you don't have permission to edit this project</div>
				@else
					<button type="submit" class="btn btn-primary" id="submit_create">Submit</button>
				@endif
			</div>
			@if ( 'file' == $field[ 'type' ] )
			<!--
				<div class="video-container">
					<video class="video" controls>
						<source src="{{ $video }}" type="video/mp4">
					</video>
					<div class="controles-video">
						<button type="button" class="btn btn-primary">Go to hotpoints editor</button>
						<button type="button" class="btn btn-primary">Go to player</button>
					</div>
				</div>
-->
			@endif
		</div>

	</form>
</div>