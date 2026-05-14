<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'channel', 'subject', 'content', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function queue(): HasMany { return $this->hasMany(CommunicationQueue::class, 'template_id'); }
}
