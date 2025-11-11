<?php

namespace App\Helpers;

class TabCounter {
    protected static int $count = 0;

    public static function incrementAndGet(): int {
        return ++self::$count;
    }

    public static function reset(): void {
        self::$count = 0;
    }
}