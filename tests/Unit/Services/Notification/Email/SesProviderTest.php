<?php

namespace Tests\Unit\Services\Notification\Email;

use App\Services\Notification\Email\SesProvider;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Tests\ModuleTestCase;

class SesProviderTest extends ModuleTestCase
{
    private SesProvider $provider;

    private array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'key' => 'AKIA-test',
            'secret' => 'test-secret',
            'region' => 'us-east-1',
            'from_name' => 'VetEssence',
        ];

        $this->provider = new SesProvider($this->config);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('Amazon SES', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->expects($this->once())->method('alwaysFrom')->willReturnSelf();
        $mailerMock->expects($this->once())->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-ses')
            ->once()
            ->andReturn($mailerMock);

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Welcome',
            '<h1>Hello</h1>',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Amazon SES', $result->provider);
        $this->assertNull($result->error);
    }

    public function test_send_with_attachments(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->expects($this->once())->method('alwaysFrom')->willReturnSelf();
        $mailerMock->expects($this->once())->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-ses')
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
            ->with('dynamic-ses')
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
            ->with('dynamic-ses')
            ->once()
            ->andThrow(new \Exception('SES credentials invalid'));

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Welcome',
            '<h1>Hello</h1>',
        );

        $this->assertFalse($result->success);
        $this->assertEquals('Amazon SES', $result->provider);
        $this->assertStringContainsString('credentials', $result->error ?? '');
    }

    public function test_uses_dynamic_mailer_config(): void
    {
        $mailerMock = $this->createMock(Mailer::class);
        $mailerMock->method('alwaysFrom')->willReturnSelf();
        $mailerMock->method('send');

        Mail::shouldReceive('mailer')
            ->with('dynamic-ses')
            ->once()
            ->andReturn($mailerMock);

        $this->provider->send('from@test.com', 'to@test.com', 'Subj', 'Body');

        $this->assertEquals('ses', config('mail.mailers.dynamic-ses.transport'));
        $this->assertEquals('AKIA-test', config('mail.mailers.dynamic-ses.key'));
        $this->assertEquals('us-east-1', config('mail.mailers.dynamic-ses.region'));
    }
}
