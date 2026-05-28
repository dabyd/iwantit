<x-layouts.app title="Roles">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Role: {{ $role->name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Role Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ $role->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="can_manage_all_users" value="1" {{ $role->can_manage_all_users ? 'checked' : '' }}>
                                Can manage all users
                            </label>
                            <small class="form-text text-muted">
                                If enabled, users with this role can see and edit all users in the system.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="can_manage_own_users" value="1" {{ $role->can_manage_own_users ? 'checked' : '' }}>
                                Can manage own users
                            </label>
                            <small class="form-text text-muted">
                                If enabled, users with this role can see and edit users created by them (client_id relationship).
                            </small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Update Role</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
