<?php

namespace Tests\Unit\Services;

use App\Services\PixService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PixServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_payload_format(): void
    {
        $service = app(PixService::class);
        $this->assertEquals('01', $service->getPayloadFormat());
    }

    public function test_get_merchant_category_code(): void
    {
        $service = app(PixService::class);
        $this->assertEquals('0000', $service->getMerchantCategoryCode());
    }

    public function test_get_transaction_currency(): void
    {
        $service = app(PixService::class);
        $this->assertEquals('986', $service->getTransactionCurrency());
    }

    public function test_get_country_code(): void
    {
        $service = app(PixService::class);
        $this->assertEquals('BR', $service->getCountryCode());
    }

    public function test_build_payload_structure(): void
    {
        $service = app(PixService::class);
        $payload = $service->buildPayload(100.50, 'TXID123');

        $this->assertStringStartsWith('000201', $payload);
        $this->assertStringEndsWith('6304', $payload);
    }

    public function test_generate_payload_includes_crc(): void
    {
        $service = app(PixService::class);
        $payload = $service->generatePayload(50.00, 'TEST123');

        $this->assertStringContainsString('6304', $payload);
        $this->assertMatchesRegularExpression('/[A-F0-9]{4}$/', $payload);
    }

    public function test_get_crc16_returns_four_char_hex(): void
    {
        $service = app(PixService::class);
        $crc = $service->getCRC16('00020126580014br.gov.bcb.pix0136test@example.com5204000053039865802BR5925VETESSENCE CLINICA VETERINARIA6009SAO PAULO62070503***6304');

        $this->assertMatchesRegularExpression('/^[A-F0-9]{4}$/', $crc);
    }

    public function test_payload_with_zero_value_omits_amount(): void
    {
        $service = app(PixService::class);
        $payload = $service->buildPayload(0);

        $this->assertStringNotContainsString('54', $payload);
    }

    public function test_payload_with_value_includes_amount(): void
    {
        $service = app(PixService::class);
        $payload = $service->buildPayload(99.90);

        $this->assertStringContainsString('540599.90', $payload);
    }
}
