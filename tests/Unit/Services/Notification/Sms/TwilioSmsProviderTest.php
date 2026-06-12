<?php

namespace Tests\Unit\Services\Notification\Sms;

use App\Services\Notification\Sms\TwilioSmsProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class TwilioSmsProviderTest extends ModuleTestCase
{
    private TwilioSmsProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new TwilioSmsProvider([
            'account_sid' => 'AC-test-sid',
            'auth_token' => 'test-token',
            'from_number' => '+15017122661',
        ]);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('Twilio SMS', $this->provider->getName());
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
        $this->assertEquals('Twilio SMS', $result->provider);
        $this->assertEquals('SM-test-sid', $result->messageId);
    }

    public function test_send_failure_when_api_returns_error(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => Http::response([], 400),
        ]);

        $result = $this->provider->send(
            '+15017122661',
            '+5511999999999',
            'Test',
        );

        $this->assertFalse($result->success);
        $this->assertEquals('Twilio SMS', $result->provider);
        $this->assertStringContainsString('400', $result->error ?? '');
    }

    public function test_send_sets_correct_auth(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => function ($request) {
                $this->assertStringContainsString('Basic', $request->header('Authorization')[0]);

                return Http::response(['sid' => 'SM-1'], 201);
            },
        ]);

        $this->provider->send('+15017122661', '+5511999999999', 'Msg');
    }

    public function test_send_sends_as_form_data(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => function ($request) {
                $this->assertEquals('+15017122661', $request['From']);
                $this->assertEquals('+5511999999999', $request['To']);
                $this->assertEquals('Hello', $request['Body']);

                return Http::response(['sid' => 'SM-1'], 201);
            },
        ]);

        $this->provider->send('+15017122661', '+5511999999999', 'Hello');
    }

    public function test_send_failure_throws_exception(): void
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC-test-sid/Messages.json' => function () {
                throw new \Exception('Connection refused');
            },
        ]);

        $result = $this->provider->send('+15017122661', '+5511999999999', 'Test');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Connection refused', $result->error ?? '');
    }
}
