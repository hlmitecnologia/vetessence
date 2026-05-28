<?php

namespace Tests\Feature\Services\Notification;

use App\Models\Setting;
use App\Services\Notification\NotificationChannel;
use App\Services\Notification\NotificationService;
use App\Services\Notification\Email\SmtpProvider;
use App\Services\Notification\Email\MailgunProvider;
use App\Services\Notification\Email\SesProvider;
use App\Services\Notification\Email\SendGridProvider;
use App\Services\Notification\Sms\TwilioSmsProvider;
use App\Services\Notification\Sms\ZenvioSmsProvider;
use App\Services\Notification\Sms\SnsSmsProvider;
use App\Services\Notification\WhatsApp\ZapiProvider;
use App\Services\Notification\WhatsApp\WeniProvider;
use App\Services\Notification\WhatsApp\CloudApiProvider;
use App\Services\Notification\WhatsApp\TwilioWhatsAppProvider;
use App\Services\Notification\Contracts\EmailProvider;
use App\Services\Notification\Contracts\SmsProvider;
use App\Services\Notification\Contracts\WhatsAppProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_resolve_email_provider_smtp()
    {
        Setting::set('notification_email_provider', 'smtp');
        Setting::set('notification_email_smtp_host', 'smtp.example.com');

        $service = new NotificationService;
        $provider = $service->resolveEmailProvider();

        $this->assertInstanceOf(SmtpProvider::class, $provider);
    }

    public function test_resolve_email_provider_mailgun()
    {
        Setting::set('notification_email_provider', 'mailgun');

        $service = new NotificationService;
        $provider = $service->resolveEmailProvider();

        $this->assertInstanceOf(MailgunProvider::class, $provider);
    }

    public function test_resolve_email_provider_ses()
    {
        Setting::set('notification_email_provider', 'ses');

        $service = new NotificationService;
        $provider = $service->resolveEmailProvider();

        $this->assertInstanceOf(SesProvider::class, $provider);
    }

    public function test_resolve_email_provider_sendgrid()
    {
        Setting::set('notification_email_provider', 'sendgrid');

        $service = new NotificationService;
        $provider = $service->resolveEmailProvider();

        $this->assertInstanceOf(SendGridProvider::class, $provider);
    }

    public function test_resolve_email_provider_unknown()
    {
        Setting::set('notification_email_provider', 'unknown');

        $service = new NotificationService;
        $provider = $service->resolveEmailProvider();

        $this->assertNull($provider);
    }

    public function test_resolve_sms_provider_twilio()
    {
        Setting::set('notification_sms_provider', 'twilio');

        $service = new NotificationService;
        $provider = $service->resolveSmsProvider();

        $this->assertInstanceOf(TwilioSmsProvider::class, $provider);
    }

    public function test_resolve_sms_provider_zenvio()
    {
        Setting::set('notification_sms_provider', 'zenvio');

        $service = new NotificationService;
        $provider = $service->resolveSmsProvider();

        $this->assertInstanceOf(ZenvioSmsProvider::class, $provider);
    }

    public function test_resolve_sms_provider_sns()
    {
        Setting::set('notification_sms_provider', 'sns');

        $service = new NotificationService;
        $provider = $service->resolveSmsProvider();

        $this->assertInstanceOf(SnsSmsProvider::class, $provider);
    }

    public function test_resolve_sms_provider_unknown()
    {
        Setting::set('notification_sms_provider', 'unknown');

        $service = new NotificationService;
        $provider = $service->resolveSmsProvider();

        $this->assertNull($provider);
    }

    public function test_resolve_whatsapp_provider_zapi()
    {
        Setting::set('notification_whatsapp_provider', 'zapi');

        $service = new NotificationService;
        $provider = $service->resolveWhatsAppProvider();

        $this->assertInstanceOf(ZapiProvider::class, $provider);
    }

    public function test_resolve_whatsapp_provider_weni()
    {
        Setting::set('notification_whatsapp_provider', 'weni');

        $service = new NotificationService;
        $provider = $service->resolveWhatsAppProvider();

        $this->assertInstanceOf(WeniProvider::class, $provider);
    }

    public function test_resolve_whatsapp_provider_cloudapi()
    {
        Setting::set('notification_whatsapp_provider', 'cloudapi');

        $service = new NotificationService;
        $provider = $service->resolveWhatsAppProvider();

        $this->assertInstanceOf(CloudApiProvider::class, $provider);
    }

    public function test_resolve_whatsapp_provider_twilio()
    {
        Setting::set('notification_whatsapp_provider', 'twilio');

        $service = new NotificationService;
        $provider = $service->resolveWhatsAppProvider();

        $this->assertInstanceOf(TwilioWhatsAppProvider::class, $provider);
    }

    public function test_resolve_whatsapp_provider_unknown()
    {
        Setting::set('notification_whatsapp_provider', 'unknown');

        $service = new NotificationService;
        $provider = $service->resolveWhatsAppProvider();

        $this->assertNull($provider);
    }

    public function test_send_email_delegates_to_smtp_provider()
    {
        Setting::set('notification_email_provider', 'smtp');
        Setting::set('notification_email_smtp_host', 'smtp.example.com');
        Setting::set('notification_email_smtp_port', '587');

        $service = new NotificationService;

        $result = $service->send(NotificationChannel::Email, 'user@test.com', 'Hello', 'Subject');

        $this->assertInstanceOf(\App\Services\Notification\NotificationResult::class, $result);
        $this->assertEquals('SMTP', $result->provider);
    }

    public function test_send_email_delegates_to_sendgrid()
    {
        Setting::set('notification_email_provider', 'sendgrid');
        Setting::set('notification_email_sendgrid_api_key', 'SG.test');

        Http::fake([
            'api.sendgrid.com/v3/mail/send' => Http::response(null, 202),
        ]);

        $service = new NotificationService;

        $result = $service->send(NotificationChannel::Email, 'user@test.com', 'Hello', 'Subject');

        $this->assertTrue($result->success);
        $this->assertEquals('SendGrid', $result->provider);
    }

    public function test_send_returns_failed_without_provider()
    {
        Setting::set('notification_email_provider', '');

        $service = new NotificationService;

        $result = $service->send(NotificationChannel::Email, 'user@test.com', 'Hello');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('No provider configured', $result->error);
    }
}
