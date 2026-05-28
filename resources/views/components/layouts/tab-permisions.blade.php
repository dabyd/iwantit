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
                    <th>Access Level</th>
                    <th>Actions</th>
                </tr>
                @foreach ( $ubp[ $data->id ] as $u )
					<tr>
						<td class="listado">{{ $u[ "user_name" ] }} </td>
						<td class="listado">
							@if ($u["owner"] !== 'Project owner')
								<span class="user-role-display">{{ $u["owner"] }}</span>
							@else
								Project owner
							@endif
						</td>
						<td class="listado">
							@if ($u["owner"] !== 'Project owner')
								<select class="form-control access-level-select" data-user-id="{{ $u['user_id'] }}">
									<option value="read" {{ ($u['access_level'] ?? 'read') === 'read' ? 'selected' : '' }}>Read</option>
									<option value="write" {{ ($u['access_level'] ?? 'read') === 'write' ? 'selected' : '' }}>Write</option>
									<option value="create" {{ ($u['access_level'] ?? 'read') === 'create' ? 'selected' : '' }}>Create</option>
								</select>
							@else
								-
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