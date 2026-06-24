<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetShopConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id', 'boarding_id', 'appointment_id',
        'service_id', 'service_date', 'used_by', 'savings_amount',
    ];

    protected $casts = [
        'service_date' => 'date',
        'savings_amount' => 'decimal:2',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(PetShopSubscription::class, 'subscription_id');
    }

    public function boarding(): BelongsTo
    {
        return $this->belongsTo(Boarding::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
