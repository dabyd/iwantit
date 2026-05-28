<x-layouts.app title="Roles">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Permissions for Role: {{ $role->name }}</h3>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">Back to Roles</a>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @foreach ($permissions as $group => $groupPermissions)
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <strong>{{ ucfirst($group) }}</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach ($groupPermissions as $permission)
                                            <div class="col-md-3">
                                                <label class="checkbox-inline">
                                                    <input type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Update Permissions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
