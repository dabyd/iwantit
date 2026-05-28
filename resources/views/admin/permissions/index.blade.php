<x-layouts.app title="Permissions">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Permissions</h3>
                    <small class="text-muted">Permissions are auto-generated from the menu structure</small>
                </div>
                <div class="card-body">
                    @foreach ($permissions as $group => $groupPermissions)
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <strong>{{ ucfirst($group) }}</strong>
                                <span class="badge bg-light text-dark">{{ $groupPermissions->count() }} permissions</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($groupPermissions as $permission)
                                        <div class="col-md-3">
                                            <span class="badge bg-info">{{ $permission->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
