<?php

namespace Tests\Unit\Services\Notification\WhatsApp;

use App\Services\Notification\WhatsApp\TwilioWhatsAppProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class TwilioWhatsAppProviderTest extends ModuleTestCase
{
    private TwilioWhatsAppProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new TwilioWhatsAppProvider([
            'account_sid' => 'AC-test-sid',
            'auth_token' => 'test-token',
            'from_number' => '+15017122661',
        ]);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('Twilio WhatsApp', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => Http::response([
                'sid' => 'SM-test-sid',
            ], 201),
        ]);

        $result = $this->provider->send(
            '+15017122661',
            '+5511999999999',
            'Your appointment is confirmed',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Twilio WhatsApp', $result->provider);
        $this->assertEquals('SM-test-sid', $result->messageId);
    }

    public function test_send_sends_whatsapp_prefixed_numbers(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => function ($request) {
                $this->assertEquals('whatsapp:+15017122661', $request['From']);
                $this->assertEquals('whatsapp:+5511999999999', $request['To']);

                return Http::response(['sid' => 'SM-1'], 201);
            },
        ]);

        $this->provider->send('+15017122661', '+5511999999999', 'Hello');
    }

    public function test_send_sends_as_form_data(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => function ($request) {
                $this->assertEquals('Hello', $request['Body']);

                return Http::response(['sid' => 'SM-1'], 201);
            },
        ]);

        $this->provider->send('+15017122661', '+5511999999999', 'Hello');
    }

    public function test_send_failure_when_api_returns_error(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => Http::response([], 400),
        ]);

        $result = $this->provider->send('+15017122661', '+5511999999999', 'Test');

        $this->assertFalse($result->success);
        $this->assertEquals('Twilio WhatsApp', $result->provider);
        $this->assertStringContainsString('400', $result->error ?? '');
    }

    public function test_send_failure_when_exception_is_thrown(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => function () {
                throw new \Exception('Twilio connection failed');
            },
        ]);

        $result = $this->provider->send('+15017122661', '+5511999999999', 'Test');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Twilio connection failed', $result->error ?? '');
    }
}
