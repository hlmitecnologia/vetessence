<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait DigitalSignable
{
    public function sign(): void
    {
        $user = Auth::user();
        $fresh = $this->fresh();
        $payload = $fresh ? $fresh->getSignaturePayload() : $this->getSignaturePayload();
        $hash = hash('sha256', $payload);

        $this->content_hash = $hash;
        $this->digital_signature = $this->buildSignature($hash, $user);
        $this->signed_at = now();
        $this->save();
    }

    public function getSignaturePayload(): string
    {
        $data = $this->attributesToArray();
        unset($data['content_hash'], $data['digital_signature'], $data['signed_at'], $data['updated_at'], $data['created_at']);
        ksort($data);
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function verifyIntegrity(): bool
    {
        if (!$this->content_hash) {
            return false;
        }
        $fresh = $this->fresh();
        $payload = $fresh ? $fresh->getSignaturePayload() : $this->getSignaturePayload();
        return hash('sha256', $payload) === $this->content_hash;
    }

    public function isSigned(): bool
    {
        return !is_null($this->signed_at);
    }

    protected function buildSignature(string $hash, $user): string
    {
        $crmv = $user->crmv ?? 'SEM_CRMV';
        return sprintf(
            "assinado:%s:%s:%s:%s",
            $user->id,
            $crmv,
            $hash,
            now()->toIso8601String()
        );
    }
}
