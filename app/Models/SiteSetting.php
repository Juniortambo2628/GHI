<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    public static function grouped(array $defaults = []): array
    {
        $settings = self::query()->get()->keyBy('key');

        return collect($defaults)->mapWithKeys(function ($default, $key) use ($settings) {
            return [$key => $settings->get($key)?->value ?? $default];
        })->all();
    }
}
