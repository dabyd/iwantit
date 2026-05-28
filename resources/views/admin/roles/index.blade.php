<x-layouts.app title="Roles">

    <div class="row">
        <div class="header">
            <div class="pull-left">
                <h2>Roles</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('roles.create') }}">Create new role</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Manage All Users</th>
            <th>Manage Own Users</th>
            <th>Permissions Count</th>
            <th>Actions</th>
        </tr>
        @foreach ($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role->id) }}">{{ $role->name }}</a>
                </td>
                <td>{{ $role->description ?? '-' }}</td>
                <td>
                    @if ($role->can_manage_all_users)
                        <span class="badge bg-success">Yes</span>
                    @else
                        <span class="badge bg-secondary">No</span>
                    @endif
                </td>
                <td>
                    @if ($role->can_manage_own_users)
                        <span class="badge bg-success">Yes</span>
                    @else
                        <span class="badge bg-secondary">No</span>
                    @endif
                </td>
                <td>{{ $role->permissions->count() }}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('roles.permissions', $role->id) }}">Permissions</a>
                    <a class="btn btn-primary" href="{{ route('roles.edit', $role->id) }}">Edit</a>
                    @if ($role->name !== 'Admin')
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</x-layouts.app>
