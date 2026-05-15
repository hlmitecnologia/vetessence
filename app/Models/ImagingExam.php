<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;
use Illuminate\Support\Str;

class ImagingExam extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'exam_number', 'pet_id', 'vet_id', 'appointment_id',
        'exam_type', 'region', 'findings', 'impression', 'recommendations',
        'images', 'status', 'radiologist_id', 'exam_date', 'branch_id',
    ];

    protected $casts = [
        'images' => 'array',
        'exam_date' => 'date',
    ];

    public static function generateNumber(): string
    {
        return 'IMG-' . date('Ymd') . '-' . str_pad(self::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function vet(): BelongsTo { return $this->belongsTo(User::class, 'vet_id'); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }
    public function radiologist(): BelongsTo { return $this->belongsTo(User::class, 'radiologist_id'); }
}
