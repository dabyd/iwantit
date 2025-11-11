<?php
    use App\Helpers\OptionHelper;
    use Illuminate\Support\Facades\Auth;

    $op = explode( '.', $route );
    $current = explode( '.', Route::current()->getName() );
    $active = 'text-white';
    if ( $op[ 0 ] == $current[ 0 ] ) {
        $active = 'active';
    }

    if ( OptionHelper::canAccess( $name, 'menu', Auth::user() ) ) {
?>
        <li class="nav-item">
            <a href="{{ route( $route ) }}" class="nav-link {{ $active }}" aria-current="page">
                <i class="fa-solid fa-{{ $icon }}"></i>&nbsp;&nbsp;&nbsp;
                {{ $name }}
            </a>
        </li>
<?php
    }