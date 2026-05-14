<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabEquipmentIntegration extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'name', 'equipment_type', 'protocol', 'endpoint_url',
        'api_key', 'ip_address', 'port', 'is_active',
        'config', 'notes', 'branch_id', 'last_contact_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'last_contact_at' => 'datetime',
    ];

    public function results(): HasMany { return $this->hasMany(LabEquipmentResult::class, 'integration_id'); }
}

class LabEquipmentResult extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'integration_id', 'result_identifier', 'pet_id',
        'laboratory_order_id', 'test_type', 'raw_data',
        'parsed_results', 'status', 'error_message', 'received_at',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'parsed_results' => 'array',
        'received_at' => 'datetime',
    ];

    public function integration() { return $this->belongsTo(LabEquipmentIntegration::class, 'integration_id'); }
    public function pet() { return $this->belongsTo(Pet::class); }
    public function laboratoryOrder() { return $this->belongsTo(LaboratoryOrder::class); }
}
