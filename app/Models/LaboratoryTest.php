<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaboratoryTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'laboratory_order_id', 'test_name', 'test_code',
        'result', 'reference_range', 'unit', 'is_abnormal', 'observations', 'branch_id',
    ];

    protected $casts = ['is_abnormal' => 'boolean'];

    public function laboratoryOrder(): BelongsTo { return $this->belongsTo(LaboratoryOrder::class); }
}
