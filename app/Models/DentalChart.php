<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DentalChart extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id', 'vet_id', 'appointment_id', 'examination_date',
        'procedure_type', 'tartar_index', 'gingivitis_index', 'halitosis', 'general_notes',
    ];

    protected $casts = ['examination_date' => 'date'];

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function vet(): BelongsTo { return $this->belongsTo(User::class, 'vet_id'); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function conditions(): HasMany { return $this->hasMany(DentalCondition::class); }
}
