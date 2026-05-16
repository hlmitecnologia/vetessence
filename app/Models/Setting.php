<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $s = static::find($key);
        return $s ? $s->value : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(compact('key'), compact('value'));
    }
}
