<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvenioCoverageRule extends Model
{
    use HasFactory;

    protected $table = 'convenio_coverage_rules';

    protected $fillable = [
        'convenio_id', 'item_type', 'service_id',
        'coverage_percent', 'max_value',
        'requires_pre_authorization', 'annual_limit',
    ];

    protected $casts = [
        'coverage_percent' => 'decimal:2',
        'max_value' => 'decimal:2',
        'requires_pre_authorization' => 'boolean',
        'annual_limit' => 'integer',
    ];

    public function convenio(): BelongsTo
    {
        return $this->belongsTo(Convenio::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
