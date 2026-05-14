<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ConsentTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'content', 'category', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted()
    {
        static::creating(fn ($t) => $t->slug = $t->slug ?: Str::slug($t->name));
    }

    public function consentForms(): HasMany { return $this->hasMany(ConsentForm::class); }
}
