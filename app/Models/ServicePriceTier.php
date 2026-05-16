<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePriceTier extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'species', 'size', 'price'];

    protected $casts = ['price' => 'decimal:2'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public static function getPrice(int $serviceId, string $species, ?string $size = null): ?float
    {
        $tier = static::where('service_id', $serviceId)
            ->where('species', $species)
            ->where('size', $size)
            ->first();

        if (!$tier && $size) {
            $tier = static::where('service_id', $serviceId)
                ->where('species', $species)
                ->whereNull('size')
                ->first();
        }

        if (!$tier) return null;
        return (float) $tier->price;
    }
}
