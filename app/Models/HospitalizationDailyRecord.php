<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalizationDailyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospitalization_id', 'user_id', 'record_date', 'shift',
        'subjective', 'objective', 'assessment', 'plan',
        'temperature', 'heart_rate', 'respiratory_rate',
        'appetite', 'hydration', 'urination', 'defecation',
        'medications_given', 'observations',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public function hospitalization(): BelongsTo { return $this->belongsTo(Hospitalization::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
