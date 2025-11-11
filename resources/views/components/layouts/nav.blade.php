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
        <x-layouts.option route="users.index" name="Users" icon="users" />
        <x-layouts.option route="projects.index" name="Projects" icon="file-video" />
        <x-layouts.option route="hotpoints.index" name="Hotpoints" icon="file-video" />
        <x-layouts.option route="tags.index" name="Tags" icon="tags" />
        <x-layouts.option route="territories.index" name="Territories" icon="globe" />
        <li class="nav-item">
            <a href="#" onclick="this.closest('form').submit()" class="nav-link disabled">
                <i class="fa-brands fa-docker"></i>&nbsp;&nbsp;&nbsp;
                Producer
            </a>
        </li>
        <li class="nav-item">
            <a href="#" onclick="this.closest('form').submit()" class="nav-link disabled">
                <i class="fa-brands fa-docker"></i>&nbsp;&nbsp;&nbsp;
                Platforms
            </a>
        </li>
        <x-layouts.option route="brands.index" name="Brands" icon="copyright" />
        <x-layouts.option route="products.index" name="Products" icon="box-archive" />
        <hr/>
        <x-layouts.option route="options.index" name="Security items" icon="globe" />

        <x-layouts.option route="datision-parameters.index" name="AI Machine CFG" icon="comment-nodes" />
<?php
/*
        <x-layouts.option route="hotpoints.index" name="Hotpoints" icon="location-dot" />
        <x-layouts.option route="languages.index" name="Languages" icon="language" />
*/
?>
        <hr/>
        <li class="nav-item">
            <a href="#" onclick="this.closest('form').submit()" class="nav-link disabled">
                <i class="fa-solid fa-gear"></i>&nbsp;&nbsp;&nbsp;
                Configuration
            </a>
        </li>
        <hr/>
        <li class="nav-item">
            <a href="/player" class="nav-link text-white" target="_blank">
                <i class="fa-solid fa-video"></i>&nbsp;&nbsp;&nbsp;
                Player
            </a>
        </li>
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
