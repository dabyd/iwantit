<x-layouts.app title="Permission: {{ $permission->name }}">

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Permission Details</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">ID</th>
                            <td>{{ $permission->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $permission->name }}</td>
                        </tr>
                        <tr>
                            <th>Guard Name</th>
                            <td>{{ $permission->guard_name }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $permission->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $permission->updated_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Back to Permissions</a>
    </div>
</x-layouts.app>
