<?php

namespace Tests\Unit\Services\Notification\Email;

use App\Services\Notification\Email\SmtpProvider;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Tests\ModuleTestCase;

class SmtpProviderTest extends ModuleTestCase
{
    private SmtpProvider $provider;

    private array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'host' => 'smtp.example.com',
            'port' => 587,
            'username' => 'user',
            'password' => 'pass',
            'encryption' => 'tls',
            'from_name' => 'VetEssence',
        ];

        $this->provider = new SmtpProvider($this->config);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('SMTP', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->expects($this->once())->method('alwaysFrom')->willReturnSelf();
        $mailerMock->expects($this->once())->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-smtp')
            ->once()
            ->andReturn($mailerMock);

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Welcome',
            '<h1>Hello</h1>',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('SMTP', $result->provider);
        $this->assertNull($result->error);
    }

    public function test_send_with_attachments(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->expects($this->once())->method('alwaysFrom')->willReturnSelf();
        $mailerMock->expects($this->once())->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-smtp')
            ->once()
            ->andReturn($mailerMock);

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Invoice',
            '<p>Invoice attached</p>',
            [['data' => 'PDF content', 'name' => 'invoice.pdf']],
        );

        $this->assertTrue($result->success);
    }

    public function test_send_with_file_path_attachment(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->expects($this->once())->method('alwaysFrom')->willReturnSelf();
        $mailerMock->expects($this->once())->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-smtp')
            ->once()
            ->andReturn($mailerMock);

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Report',
            '<p>Report attached</p>',
            [['path' => '/tmp/test.txt', 'name' => 'test.txt']],
        );

        $this->assertTrue($result->success);
    }

    public function test_send_failure_returns_failed_result(): void
    {
        Mail::shouldReceive('mailer')
            ->with('dynamic-smtp')
            ->once()
            ->andThrow(new \Exception('SMTP connection refused'));

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Welcome',
            '<h1>Hello</h1>',
        );

        $this->assertFalse($result->success);
        $this->assertEquals('SMTP', $result->provider);
        $this->assertStringContainsString('connection refused', strtolower($result->error ?? ''));
    }

    public function test_uses_dynamic_mailer_config(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->method('alwaysFrom')->willReturnSelf();
        $mailerMock->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-smtp')
            ->once()
            ->andReturn($mailerMock);

        $this->provider->send('from@test.com', 'to@test.com', 'Subj', 'Body');

        $this->assertEquals('smtp', config('mail.mailers.dynamic-smtp.transport'));
        $this->assertEquals('smtp.example.com', config('mail.mailers.dynamic-smtp.host'));
        $this->assertEquals(587, config('mail.mailers.dynamic-smtp.port'));
        $this->assertEquals('tls', config('mail.mailers.dynamic-smtp.encryption'));
    }
}
