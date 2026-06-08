<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccination extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'pet_id', 'vaccine', 'batch', 'date', 'next_date',
        'lot', 'manufacturer', 'application_site', 'vet_id',
        'medical_record_id', 'product_id', 'notes', 'branch_id'
    ];

    protected $casts = [
        'date' => 'date',
        'next_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
