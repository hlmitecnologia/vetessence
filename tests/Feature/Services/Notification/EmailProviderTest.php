<?php

namespace Tests\Feature\Services\Notification;

use App\Services\Notification\Email\MailerSendProvider;
use App\Services\Notification\Email\MailgunProvider;
use App\Services\Notification\Email\SendGridProvider;
use App\Services\Notification\Email\SesProvider;
use App\Services\Notification\Email\SmtpProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailProviderTest extends TestCase
{
    public function test_smtp_send_returns_notification_result()
    {
        $provider = new SmtpProvider([
            'host' => 'smtp.example.com',
            'port' => 587,
            'username' => 'user',
            'password' => 'pass',
            'encryption' => 'tls',
        ]);

        $result = $provider->send('from@test.com', 'to@test.com', 'Subject', 'Body');

        $this->assertInstanceOf(NotificationResult::class, $result);
        $this->assertEquals('SMTP', $result->provider);
    }

    public function test_mailgun_sends_via_laravel_mail()
    {
        $provider = new MailgunProvider([
            'domain' => 'mg.example.com',
            'secret' => 'key-test',
            'endpoint' => 'api.mailgun.net',
        ]);

        $result = $provider->send('from@test.com', 'to@test.com', 'Subject', 'Body');

        $this->assertInstanceOf(NotificationResult::class, $result);
        $this->assertEquals('Mailgun', $result->provider);
    }

    public function test_ses_sends_via_laravel_mail()
    {
        $provider = new SesProvider([
            'key' => 'AKID',
            'secret' => 'secret',
            'region' => 'us-east-1',
        ]);

        $result = $provider->send('from@test.com', 'to@test.com', 'Subject', 'Body');

        $this->assertInstanceOf(NotificationResult::class, $result);
        $this->assertEquals('Amazon SES', $result->provider);
    }

    public function test_mailersend_send_success()
    {
        $provider = new MailerSendProvider([
            'api_key' => 'mlsn.test',
        ]);

        $result = $provider->send('from@test.com', 'to@test.com', 'Subject', 'Body');

        $this->assertInstanceOf(NotificationResult::class, $result);
        $this->assertEquals('MailerSend', $result->provider);
    }

    public function test_mailersend_send_failure()
    {
        $provider = new MailerSendProvider([
            'api_key' => '',
        ]);

        $result = $provider->send('from@test.com', 'to@test.com', 'Subject', 'Body');

        $this->assertInstanceOf(NotificationResult::class, $result);
        $this->assertFalse($result->success);
    }

    public function test_sendgrid_send_success()
    {
        Http::fake([
            'api.sendgrid.com/v3/mail/send' => Http::response(null, 202),
        ]);

        $provider = new SendGridProvider([
            'api_key' => 'SG.test',
        ]);

        $result = $provider->send('from@test.com', 'to@test.com', 'Subject', 'Body');

        $this->assertTrue($result->success);
        $this->assertEquals('SendGrid', $result->provider);
    }

    public function test_sendgrid_send_failure()
    {
        Http::fake([
            'api.sendgrid.com/v3/mail/send' => Http::response(null, 401),
        ]);

        $provider = new SendGridProvider([
            'api_key' => 'SG.test',
        ]);

        $result = $provider->send('from@test.com', 'to@test.com', 'Subject', 'Body');

        $this->assertFalse($result->success);
    }
}
