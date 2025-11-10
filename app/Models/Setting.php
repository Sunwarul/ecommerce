<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    public static function get($key, $default = null)
    {
        return cache()->rememberForever("setting_{$key}", function () use ($key, $default) {
            return optional(static::where('key', $key)->first())->value ?? $default;
        });
    }

    public static function set($key, $value)
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting_{$key}");
    }

    public static function remove($key)
    {
        static::where('key', $key)->delete();
        cache()->forget("setting_{$key}");
    }

    public function setKeyAttribute($key)
    {
        $this->attributes['key'] = Str::slug($key, "_");
    }
}
