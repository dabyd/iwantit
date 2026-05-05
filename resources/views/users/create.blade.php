<?php
    use Illuminate\Support\Facades\Auth;
?>
<x-layouts.app title="Users: Add new user">
    <div class="header">
        <div class="pull-left">
            <h2>Create new user</h2>
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

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="iwt-user-form row">
            <!-- Name -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Name">
                </div>
            </div>

            <!-- Email -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>EMail:</strong>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="E-Mail">
                </div>
            </div>

            <!-- Password -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Password:</strong>
                    <input name="password" type="password" class="form-control" placeholder="Password">
                </div>
            </div>

            <!-- Role -->
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Role:</strong>
                    <select name="role" class="form-control" {{ Auth::user()->role != 'admin' ? 'disabled' : '' }}>
                        <option value="editor"  {{ old('role') == 'editor'  ? 'selected' : '' }}>Editor</option>
                        <option value="super"   {{ old('role') == 'super'   ? 'selected' : '' }}>Supervisor</option>
                        <option value="admin"   {{ old('role') == 'admin'   ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>

            <!-- Grants -->
            @if($options->isNotEmpty())
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Grants:</strong>
                    @foreach($options as $type => $typeOptions)
                        <div class="option-group">
                            <h4>{{ ucfirst($type) }}</h4>
                            @foreach($typeOptions as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="options[]"
                                           value="{{ $option->id }}"
                                           id="option-{{ $option->id }}"
                                           {{ in_array($option->id, old('options', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="option-{{ $option->id }}">
                                        {{ $option->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Submit -->
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button id="submit_create" type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </form>
    <div class="cobertura" style="display:none;">
        <div class="iwt-spinner"></div>
    </div>
</x-layouts.app>
