<?php

namespace Tests\Feature\Services\Notification;

use App\Services\Notification\WhatsApp\CloudApiProvider;
use App\Services\Notification\WhatsApp\TwilioWhatsAppProvider;
use App\Services\Notification\WhatsApp\WeniProvider;
use App\Services\Notification\WhatsApp\ZapiProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppProviderTest extends TestCase
{
    public function test_zapi_send_success()
    {
        Http::fake([
            'api.z-api.io/v1/instances/*/send-text' => Http::response(['id' => 'MSG-001'], 200),
        ]);

        $provider = new ZapiProvider([
            'api_url' => 'https://api.z-api.io/v1',
            'api_token' => 'zapi-token',
            'instance' => 'inst-1',
        ]);

        $result = $provider->send('Clinic', '5511888888888', 'Your appointment is confirmed');

        $this->assertTrue($result->success);
        $this->assertEquals('Z-API', $result->provider);
    }

    public function test_zapi_send_failure()
    {
        Http::fake([
            'api.z-api.io/v1/instances/*/send-text' => Http::response(null, 401),
        ]);

        $provider = new ZapiProvider([
            'api_url' => 'https://api.z-api.io/v1',
            'api_token' => 'zapi-token',
            'instance' => 'inst-1',
        ]);

        $result = $provider->send('Clinic', '5511888888888', 'Message');

        $this->assertFalse($result->success);
    }

    public function test_weni_send_success()
    {
        Http::fake([
            'api.iaweni.com.br/v1/projects/*/messages' => Http::response(['id' => 'MSG-001'], 200),
        ]);

        $provider = new WeniProvider([
            'api_key' => 'weni-key',
            'project_uuid' => 'proj-uuid',
            'from_number' => '5511999999999',
        ]);

        $result = $provider->send('Clinic', '5511888888888', 'Your vaccine is due');

        $this->assertTrue($result->success);
        $this->assertEquals('Weni', $result->provider);
    }

    public function test_weni_send_failure()
    {
        Http::fake([
            'api.iaweni.com.br/v1/projects/*/messages' => Http::response(null, 500),
        ]);

        $provider = new WeniProvider([
            'api_key' => 'weni-key',
            'project_uuid' => 'proj-uuid',
            'from_number' => '5511999999999',
        ]);

        $result = $provider->send('Clinic', '5511888888888', 'Message');

        $this->assertFalse($result->success);
    }

    public function test_cloudapi_send_success()
    {
        Http::fake([
            'graph.facebook.com/v21.0/*/messages' => Http::response([
                'messages' => [['id' => 'wamid.ABC123']],
            ], 200),
        ]);

        $provider = new CloudApiProvider([
            'access_token' => 'EAATest',
            'phone_number_id' => '123456789',
        ]);

        $result = $provider->send('Clinic', '5511888888888', 'Your pet is ready for discharge');

        $this->assertTrue($result->success);
        $this->assertEquals('WhatsApp Cloud API', $result->provider);
        $this->assertEquals('wamid.ABC123', $result->messageId);
    }

    public function test_cloudapi_send_failure()
    {
        Http::fake([
            'graph.facebook.com/v21.0/*/messages' => Http::response(null, 403),
        ]);

        $provider = new CloudApiProvider([
            'access_token' => 'EAATest',
            'phone_number_id' => '123456789',
        ]);

        $result = $provider->send('Clinic', '5511888888888', 'Message');

        $this->assertFalse($result->success);
    }

    public function test_twilio_whatsapp_send_success()
    {
        Http::fake([
            'api.twilio.com/2010-04-01/Accounts/*/Messages.json' => Http::response([
                'sid' => 'SM-WA-123',
                'status' => 'sent',
            ], 201),
        ]);

        $provider = new TwilioWhatsAppProvider([
            'account_sid' => 'AC123',
            'auth_token' => 'token',
            'from_number' => 'whatsapp:+14155238886',
        ]);

        $result = $provider->send('Clinic', 'whatsapp:+5511888888888', 'Your appointment is tomorrow');

        $this->assertTrue($result->success);
        $this->assertEquals('Twilio WhatsApp', $result->provider);
        $this->assertEquals('SM-WA-123', $result->messageId);
    }

    public function test_twilio_whatsapp_send_failure()
    {
        Http::fake([
            'api.twilio.com/2010-04-01/Accounts/*/Messages.json' => Http::response(null, 401),
        ]);

        $provider = new TwilioWhatsAppProvider([
            'account_sid' => 'AC123',
            'auth_token' => 'token',
            'from_number' => 'whatsapp:+14155238886',
        ]);

        $result = $provider->send('Clinic', 'whatsapp:+5511888888888', 'Message');

        $this->assertFalse($result->success);
    }
}
