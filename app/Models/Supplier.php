<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'name', 'cnpj', 'ie', 'phone', 'email',
        'address', 'city', 'state', 'contact', 'notes', 'branch_id'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
