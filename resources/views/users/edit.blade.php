<?php
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

$superUsers = User::role('Supervisor')->get();
$activeUserId = $user->client_id;

$roles = Role::all();

$userProjectPermissions = DB::table('project_user_permissions')
    ->where('user_id', $user->id)
    ->pluck('access_level', 'project_id');
?>
<x-layouts.app title="Users">
    <div class="header">
        <div class="pull-left">
            <h2>Edit user</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
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

    <form action="{{ route('users.update',$user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="iwt-user-form row">
            <!-- Name -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" value="{{ $user->name }}" class="form-control" placeholder="Name">
                </div>
            </div>

            <!-- Email -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>EMail:</strong>
                    <input type="text" name="email" value="{{ $user->email }}" class="form-control" placeholder="E-Mail">
                </div>
            </div>

            <!-- Password -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Password:</strong>
                    <input name="password" type="password" class="form-control" placeholder="Password">
                </div>
            </div>

            <!-- Rol -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Role:</strong>
                    <select name="role" class="form-control">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Project Permissions -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Project Permissions:</strong>
                    <small class="text-muted d-block mb-2">Note: Admin role has access to all projects automatically</small>
                    <div class="mb-3">
                        <input type="text" id="project-search" class="form-control" placeholder="Filter projects by name (min 3 chars)..." autocomplete="off">
                    </div>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Access Level</th>
                            </tr>
                        </thead>
                        <tbody id="projects-tbody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('project-search');
        const tbody = document.getElementById('projects-tbody');
        const userId = {{ $user->id }};
        let debounceTimer;

        function loadProjects(search = '') {
            const url = '/users/' + userId + '/search-projects?search=' + encodeURIComponent(search) + '&user_id=' + userId;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = data.html;
                })
                .catch(error => console.error('Error loading projects:', error));
        }

        loadProjects();

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                loadProjects(searchInput.value);
            }, 300);
        });
    });
    </script>
</x-layouts.app>