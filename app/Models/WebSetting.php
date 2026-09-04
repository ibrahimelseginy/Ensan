<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WebSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $group = 'general', $type = 'text')
    {
        return self::updateOrCreate(
        ['key' => $key],
        ['value' => $value, 'group' => $group, 'type' => $type]
        );
    }
}
