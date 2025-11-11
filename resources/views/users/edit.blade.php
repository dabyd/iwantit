<?php
    use App\Helpers\OptionHelper;
    use Illuminate\Support\Facades\Auth;
    use App\Models\User;
    use App\Models\Options;
    use App\Models\UserOption;

    // Obtener los usuarios con rol 'super'
    $superUsers = User::where('role', 'super')->get();
    $activeUserId = $user->client_id; // ID del usuario activo

    // Obtener las opciones agrupadas por tipo
    $options = Options::all()->groupBy('type');

    // Obtener los registros de user_options para el usuario actual
    $userOptions = UserOption::where('user_id', $user->id)->get()->keyBy('option_id');
?>
<x-layouts.app title="Users: Edit user">
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
                    <select name="role" class="form-control"{{ ( Auth::user()->role != 'admin' ) ? 'disabled read-only:' : '' }}>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super" {{ $user->role == 'super' ? 'selected' : '' }}>Supervisor</option>
                        <option value="editor" {{ $user->role == 'editor' ? 'selected' : '' }}>Editor</option>
                    </select>
                </div>
            </div>
            
            <!-- Permisos -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Grants:</strong>
                    @foreach($options as $type => $typeOptions)
                        <div class="option-group">
                            <h4>{{ ucfirst($type) }}</h4>
                            @foreach($typeOptions as $option)
                                <?php
                                    $isChecked = isset($userOptions[$option->id]) && $userOptions[$option->id]->active;
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="options[]" value="{{ $option->id }}" id="option-{{ $option->id }}" {{ $isChecked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="option-{{ $option->id }}">
                                        {{ $option->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Projects -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Projects:</strong>
                    @foreach($options as $type => $typeOptions)
                        <div class="option-group">
                            <h4>{{ ucfirst($type) }}</h4>
                            @foreach($typeOptions as $option)
                                <?php
                                    $isChecked = isset($userOptions[$option->id]) && $userOptions[$option->id]->active;
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="options[]" value="{{ $option->id }}" id="option-{{ $option->id }}" {{ $isChecked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="option-{{ $option->id }}">
                                        {{ $option->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </form>
</x-layouts.app>
