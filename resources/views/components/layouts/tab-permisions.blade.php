@props( [ 'data', 'ubp' ] )
@php
	$currentCount = \App\Helpers\TabCounter::incrementAndGet();
@endphp

@isset( $ubp )
    <div class="tab-{{ $currentCount }}">
        <h2>Project permissions</h2>
        <h3>Users with access to this project ({{ $data->name }})</h3>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>User name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                @foreach ( $ubp[ $data->id ] as $u )
					<tr>
						<td class="listado">{{ $u[ "user_name" ] }} </td>
						<td class="listado">
							@if ($u["owner"] !== 'Project owner')
								@if ($u["user_role"] == 'super' && $u["owner"] !== 'Editor')
									<select class="form-control user-role-select" data-user-id="{{ $u['user_id'] }}">
										<option value="shared_owner" {{ $u["owner"] === 'Shared owner' ? 'selected' : '' }}>Shared owner</option>
										<option value="NO" {{ $u["owner"] === 'Editor' ? 'selected' : '' }}>Editor</option>
									</select>
								@else
									Editor
								@endif
							@else
								Project owner
							@endif
						</td>
						<td class="listado">
							@if ( 'Project owner' != $u[ "owner" ] )
								<button class="btn btn-danger" id="delete_user_project" data-user-id="{{$u[ 'user_id' ]}}">Delete</button>
							@endif
						</td>
					</tr>
                @endforeach
            </tbody>
        </table>
        <input type="hidden" id="projectId" value="{{ $data->id }}" />
        @php
			$currentUser = auth()->user();
			$userRoleInProject = collect($ubp[$data->id])->firstWhere('user_id', $currentUser->id)['owner'] ?? null;
        @endphp
        @if (in_array($userRoleInProject, ['Project owner', 'Shared owner']))
        	<button type="button" class="btn btn-primary" id="add_user_project">Add users to this project</button>
        @endif
    </div>
@endisset