<?php

namespace Tests\Unit\Services;

use App\Services\EmailApiService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailApiServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_service_can_be_instantiated(): void
    {
        $service = app(EmailApiService::class);
        $this->assertInstanceOf(EmailApiService::class, $service);
    }

    public function test_send_returns_true_on_success(): void
    {
        Http::fake(['*' => Http::response(['message' => 'OK'], 200)]);

        $result = app(EmailApiService::class)->send('John', 'john@example.com', 'Hello');

        $this->assertTrue($result);
    }

    public function test_send_returns_false_on_http_error(): void
    {
        Http::fake(['*' => Http::response(['error' => 'Bad Request'], 400)]);

        $result = app(EmailApiService::class)->send('John', 'john@example.com', 'Hello');

        $this->assertFalse($result);
    }

    public function test_send_returns_false_on_exception(): void
    {
        Http::fake(function () {
            throw new \Exception('Connection failed');
        });

        $result = app(EmailApiService::class)->send('John', 'john@example.com', 'Hello');

        $this->assertFalse($result);
    }
}
