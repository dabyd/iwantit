<x-layouts.app title="Roles">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Role</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">Role Name</label>
                            <input type="text" name="name" id="name" class="form-control" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="can_manage_all_users" value="1">
                                Can manage all users
                            </label>
                            <small class="form-text text-muted">
                                If enabled, users with this role can see and edit all users in the system.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="can_manage_own_users" value="1" checked>
                                Can manage own users
                            </label>
                            <small class="form-text text-muted">
                                If enabled, users with this role can see and edit users created by them (client_id relationship).
                            </small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Create Role</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
