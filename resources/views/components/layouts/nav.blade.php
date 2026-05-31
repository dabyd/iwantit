<div class="iwt-menu d-flex flex-column flex-shrink-0 p-3">
<?php
/*
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <svg class="bi pe-none me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
        <span class="fs-4">I Want It</span>
    </a>
    <hr>
*/
    $user = Auth()->user();
?>
    <ul class="iwt-options menu_left nav nav-pills flex-column mb-auto">
    @guest
        <li class="nav-item">
            <a href="{{ route( 'login' ) }}" class="nav-link text-white">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>&nbsp;&nbsp;&nbsp;
                Login
            </a>
        </li>
    @else
<?php
/*
        <x-layouts.option route="dashboard" name="Dashboard" icon="house" />
        <x-layouts.option route="projects.index" name="Projects" icon="film" />
*/
?>
        <li class="nav-item">
            <a href="#" onclick="this.closest('form').submit()" class="nav-link disabled">
                <i class="fa-solid fa-user"></i>&nbsp;&nbsp;&nbsp;{{ $user->name }}
            </a>
        </li>
        <hr/>
        @can('projects-menu')
        <x-layouts.option route="projects.index" name="Projects" icon="file-video" />
        @endcan
        @can('products-menu')
        <x-layouts.option route="products.index" name="Products" icon="box-archive" />
        @endcan
        @can('brands-menu')
        <x-layouts.option route="brands.index" name="Brands" icon="copyright" />
        @endcan
        <hr/>
        @can('users-menu')
        <x-layouts.option route="users.index" name="Users" icon="users" />
        @endcan
        @can('roles-menu')
        <x-layouts.option route="roles.index" name="Roles" icon="shield" />
        @endcan
        @can('permissions-menu')
        <x-layouts.option route="permissions.index" name="Permissions" icon="key" />
        @endcan
        <hr/>
        @can('hotpoints-menu')
        <x-layouts.option route="hotpoints.index" name="Hotpoints" icon="file-video" />
        @endcan
        @can('tags-menu')
        <x-layouts.option route="tags.index" name="Tags" icon="tags" />
        @endcan
        @can('territories-menu')
        <x-layouts.option route="territories.index" name="Territories" icon="globe" />
        @endcan
        @can('options-menu')
        <x-layouts.option route="options.index" name="Security items" icon="globe" />
        @endcan
        @can('datision-parameters-menu')
        <x-layouts.option route="datision-parameters.index" name="AI Machine CFG" icon="comment-nodes" />
        @endcan
        @if(!$user->hasTwoFactorEnabled())
        <hr/>
        <li class="nav-item">
            <a href="{{ route('two-factor.setup') }}" class="nav-link d-flex align-items-center text-warning fw-bold" style="background: rgba(255,193,7,0.12); border-left: 3px solid #ffc107;">
                <i class="fa-solid fa-shield-halved"></i>&nbsp;&nbsp;&nbsp;
                Activate your 2FA now
            </a>
        </li>
        @endif
        <hr/>
        <li class="nav-item">
            <form action="{{ route( 'logout' ) }}" method="post" style="display: inline">
                @csrf
                <a href="#" onclick="this.closest('form').submit()" class="nav-link text-white">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>&nbsp;&nbsp;&nbsp;
                    Logout
                </a>
            </form>
        </li>
    @endguest
    </ul>
<?php
/*
    <hr>
    <div class="dropdown">
    </div>
*/
?>
</div>