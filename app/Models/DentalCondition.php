<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DentalCondition extends Model
{
    use HasFactory;

    protected $fillable = ['dental_chart_id', 'tooth_number', 'quadrant', 'condition', 'severity', 'notes'];

    public function dentalChart(): BelongsTo { return $this->belongsTo(DentalChart::class); }
}
