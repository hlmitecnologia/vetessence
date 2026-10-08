<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;

class MedicationAdministration extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'product_id', 'inventory_batch_id', 'inventory_container_id', 'medication_reservation_id',
        'pet_id', 'prescribed_by', 'administered_by', 'quantity_prescribed',
        'quantity_administered', 'unit', 'route', 'status', 'source_type', 'source_id',
        'notes', 'administered_at', 'branch_id',
    ];

    protected $casts = [
        'quantity_prescribed' => 'decimal:4',
        'quantity_administered' => 'decimal:4',
        'administered_at' => 'datetime',
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function batch(): BelongsTo { return $this->belongsTo(InventoryBatch::class, 'inventory_batch_id'); }
    public function container(): BelongsTo { return $this->belongsTo(InventoryContainer::class, 'inventory_container_id'); }
    public function reservation(): BelongsTo { return $this->belongsTo(MedicationReservation::class, 'medication_reservation_id'); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function prescribedBy(): BelongsTo { return $this->belongsTo(User::class, 'prescribed_by'); }
    public function administeredBy(): BelongsTo { return $this->belongsTo(User::class, 'administered_by'); }
}
