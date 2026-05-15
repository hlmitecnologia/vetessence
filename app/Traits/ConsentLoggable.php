<?php

namespace App\Traits;

use App\Models\ConsentLog;

trait ConsentLoggable
{
    public function consentLogs()
    {
        return $this->morphMany(ConsentLog::class, 'consentable');
    }

    public function logConsent(string $type, string $purpose, bool $granted = true, ?int $userId = null): ConsentLog
    {
        return $this->consentLogs()->create([
            'user_id' => $userId,
            'type' => $type,
            'purpose' => $purpose,
            'granted' => $granted,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'consented_at' => now(),
        ]);
    }

    public function hasActiveConsent(string $type): bool
    {
        return $this->consentLogs()
            ->where('type', $type)
            ->where('granted', true)
            ->exists();
    }

    public function revokeConsent(string $type): void
    {
        $this->consentLogs()
            ->where('type', $type)
            ->where('granted', true)
            ->update(['granted' => false]);
    }
}
