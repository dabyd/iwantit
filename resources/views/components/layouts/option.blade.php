<?php
    $op = explode( '.', $route );
    $current = explode( '.', Route::current()->getName() );
    $active = 'text-white';
    if ( $op[ 0 ] == $current[ 0 ] ) {
        $active = 'active text-white';
    }
?>
    <li class="nav-item">
        <a href="{{ route( $route ) }}" class="nav-link {{ $active }} cursor-pointer" title="Go to {{ $name }} list">
            <i class="fa-solid fa-{{ $icon }}"></i>&nbsp;&nbsp;&nbsp;
            {{ $name }}
        </a>
    </li>
