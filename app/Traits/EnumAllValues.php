<?php

namespace App\Traits;

trait EnumAllValues {
    public static function getAllValues(): array {
        return array_map(
            fn(self $enum) => $enum->value, 
            self::cases()
        );
    }
}