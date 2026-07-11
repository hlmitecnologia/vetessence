<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NfeTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'from_branch_id', 'to_branch_id',
        'product_id', 'user_id',
        'nfe_number', 'nfe_key', 'status',
        'issuance_date', 'cancelled_at',
        'nfe_url_xml', 'nfe_url_pdf', 'danfe_url',
        'provider_response', 'error_message',
    ];

    protected $casts = [
        'issuance_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'provider_response' => 'json',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
