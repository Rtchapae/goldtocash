<?php

namespace App\Support;

final class LikeSearch
{
    public static function wrap(string $term): string
    {
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term);

        return '%' . $escaped . '%';
    }
}
