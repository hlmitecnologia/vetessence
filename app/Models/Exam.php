<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'pet_id', 'appointment_id', 'vet_id', 'type', 'status',
        'requested_date', 'result_date', 'result_file', 'result', 'lab_name', 'notes', 'branch_id'
    ];

    protected $casts = [
        'requested_date' => 'date',
        'result_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
