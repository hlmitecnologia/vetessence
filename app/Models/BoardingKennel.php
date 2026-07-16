<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingKennel extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'size', 'capacity', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function boardings()
    {
        return $this->hasMany(Boarding::class, 'kennel_id');
    }

    public function activeBoardings()
    {
        return $this->boardings()->whereIn('status', ['checked_in']);
    }
}
