<?php

namespace Tests\Unit\Services\Notification\Email;

use App\Services\Notification\Email\MailgunProvider;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Tests\ModuleTestCase;

class MailgunProviderTest extends ModuleTestCase
{
    private MailgunProvider $provider;

    private array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'domain' => 'mg.example.com',
            'secret' => 'key-secret',
            'endpoint' => 'api.mailgun.net',
            'from_name' => 'VetEssence',
        ];

        $this->provider = new MailgunProvider($this->config);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('Mailgun', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->expects($this->once())->method('alwaysFrom')->willReturnSelf();
        $mailerMock->expects($this->once())->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-mailgun')
            ->once()
            ->andReturn($mailerMock);

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Welcome',
            '<h1>Hello</h1>',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Mailgun', $result->provider);
        $this->assertNull($result->error);
    }

    public function test_send_with_attachments(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->expects($this->once())->method('alwaysFrom')->willReturnSelf();
        $mailerMock->expects($this->once())->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-mailgun')
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
            ->with('dynamic-mailgun')
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
            ->with('dynamic-mailgun')
            ->once()
            ->andThrow(new \Exception('Mailgun API timeout'));

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Welcome',
            '<h1>Hello</h1>',
        );

        $this->assertFalse($result->success);
        $this->assertEquals('Mailgun', $result->provider);
        $this->assertStringContainsString('timeout', $result->error ?? '');
    }

    public function test_uses_dynamic_mailer_config(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->method('alwaysFrom')->willReturnSelf();
        $mailerMock->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-mailgun')
            ->once()
            ->andReturn($mailerMock);

        $this->provider->send('from@test.com', 'to@test.com', 'Subj', 'Body');

        $this->assertEquals('mailgun', config('mail.mailers.dynamic-mailgun.transport'));
        $this->assertEquals('mg.example.com', config('mail.mailers.dynamic-mailgun.domain'));
        $this->assertEquals('key-secret', config('mail.mailers.dynamic-mailgun.secret'));
    }
}
