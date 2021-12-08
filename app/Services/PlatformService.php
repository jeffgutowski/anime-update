<?php

namespace App\Services;

use File;

class PlatformService
{
    public static function find($index, $match)
    {
        $platforms = json_decode(File::get(storage_path('app/public/platforms/platforms.json')));
        $platform = array_first($platforms, function ($value, $key) use ($index, $match) {
            if (isset($value->$index)) {
                return $value->$index === $match;
            }
        });
        return $platform;
    }

    public static function index()
    {
        return json_decode(File::get(storage_path('app/public/platforms/platforms.json')));
    }
}
