<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalizationDailyRecord extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'hospitalization_id', 'user_id', 'record_date', 'shift',
        'subjective', 'objective', 'assessment', 'plan',
        'temperature', 'heart_rate', 'respiratory_rate',
        'appetite', 'hydration', 'urination', 'defecation',
        'medications_given', 'observations', 'branch_id',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public function hospitalization(): BelongsTo { return $this->belongsTo(Hospitalization::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
