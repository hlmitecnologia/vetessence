<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LaboratoryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'pet_id', 'vet_id', 'appointment_id',
        'lab_name', 'order_date', 'result_date', 'status', 'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'result_date' => 'date',
    ];

    public static function generateNumber(): string
    {
        return 'LAB-' . date('Ymd') . '-' . str_pad(self::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function vet(): BelongsTo { return $this->belongsTo(User::class, 'vet_id'); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function tests(): HasMany { return $this->hasMany(LaboratoryTest::class); }
}
