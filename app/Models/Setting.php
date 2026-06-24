<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null): mixed
    {
        $s = static::find($key);
        return $s ? $s->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(compact('key'), compact('value'));
    }

    public static function getEncrypted(string $key, mixed $default = null): mixed
    {
        $s = static::find($key);
        if (!$s || !$s->value) {
            return $default;
        }
        try {
            return Crypt::decryptString($s->value);
        } catch (\Exception) {
            return $default;
        }
    }

    public static function setEncrypted(string $key, mixed $value): void
    {
        static::updateOrCreate(
            compact('key'),
            ['value' => $value ? Crypt::encryptString($value) : null]
        );
    }
}
