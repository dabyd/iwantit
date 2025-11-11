<?php

namespace App\Helpers;

use App\Http\Controllers\OptionController;

class OptionHelper {
    public static function canAccess( $option, $type, $user ) {
        $controller = new OptionController();
        return $controller->canAccess( $option, $type, $user );
    }
}
