<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailApiService
{
    protected string $url;
    protected string $token;
    protected int $timeout;

    public function __construct()
    {
        $this->url = config('email-api.url');
        $this->token = config('email-api.token');
        $this->timeout = config('email-api.timeout');
    }

    public function send(string $name, string $email, string $message): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->token)
                ->post($this->url, [
                    'name' => $name,
                    'email' => $email,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('Email sent via API', ['email' => $email, 'name' => $name]);
                return true;
            }

            Log::warning('Email API responded with error', [
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Email API request failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
