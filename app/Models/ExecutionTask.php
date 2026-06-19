<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExecutionTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'execution_map_id',
        'category',
        'title',
        'description',
        'scheduled_time',
        'frequency',
        'route',
        'dosage',
        'unit',
        'source_type',
        'source_id',
        'status',
        'observations',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime:H:i',
    ];

    public function executionMap(): BelongsTo
    {
        return $this->belongsTo(ExecutionMap::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ExecutionLog::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePending($q)
    {
        $q->where('status', 'pending');
    }

    public function scopeOverdue($q)
    {
        $q->whereIn('status', ['pending', 'in_progress'])
            ->where('scheduled_time', '<', now()->format('H:i:s'));
    }

    public static function parseFrequency($text): array
    {
        $map = [
            'every_8h' => [6, 14, 22],
            '8/8h' => [6, 14, 22],
            '8 em 8' => [6, 14, 22],
            'TID' => [6, 14, 22],
            'tid' => [6, 14, 22],
            'every_12h' => [8, 20],
            '12/12h' => [8, 20],
            '12 em 12' => [8, 20],
            'BID' => [8, 20],
            'bid' => [8, 20],
            'every_6h' => [0, 6, 12, 18],
            '6/6h' => [0, 6, 12, 18],
            '6 em 6' => [0, 6, 12, 18],
            'QID' => [0, 6, 12, 18],
            'qid' => [0, 6, 12, 18],
            'every_24h' => [8],
            '24h' => [8],
            '1x ao dia' => [8],
            'SID' => [8],
            'sid' => [8],
            'daily' => [8],
        ];

        $normalized = trim($text);
        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        if (preg_match('/^(\d+)\s*em\s*\1\s*h/i', $normalized, $m)) {
            return [6, 6 + (int) $m[1], 6 + 2 * (int) $m[1], 6 + 3 * (int) $m[1]];
        }

        return [8];
    }
}
