<?php

namespace Tests\Unit\Services\Notification\Email;

use App\Services\Notification\Email\SendGridProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class SendGridProviderTest extends ModuleTestCase
{
    private SendGridProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new SendGridProvider([
            'api_key' => 'SG.test-key',
            'from_name' => 'VetEssence',
        ]);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('SendGrid', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        Http::fake([
            'https://api.sendgrid.com/v3/mail/send' => Http::response([], 202),
        ]);

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Welcome',
            '<h1>Hello</h1>',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('SendGrid', $result->provider);
    }

    public function test_send_failure_when_api_returns_error(): void
    {
        Http::fake([
            'https://api.sendgrid.com/v3/mail/send' => Http::response([], 401),
        ]);

        $result = $this->provider->send(
            'no-reply@vetessence.com',
            'user@example.com',
            'Welcome',
            '<h1>Hello</h1>',
        );

        $this->assertFalse($result->success);
        $this->assertEquals('SendGrid', $result->provider);
        $this->assertStringContainsString('401', $result->error ?? '');
    }

    public function test_send_with_data_attachment(): void
    {
        Http::fake([
            'https://api.sendgrid.com/v3/mail/send' => Http::response([], 202),
        ]);

        $result = $this->provider->send(
            'from@test.com',
            'to@test.com',
            'Attachment',
            '<p>See attached</p>',
            [['data' => 'binary', 'name' => 'file.pdf', 'mime' => 'application/pdf']],
        );

        $this->assertTrue($result->success);
    }

    public function test_send_with_file_path_attachment(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'sendgrid_test_');
        file_put_contents($path, 'file content');

        Http::fake([
            'https://api.sendgrid.com/v3/mail/send' => Http::response([], 202),
        ]);

        $result = $this->provider->send(
            'from@test.com',
            'to@test.com',
            'File',
            '<p>File attached</p>',
            [['path' => $path, 'name' => 'upload.txt']],
        );

        $this->assertTrue($result->success);

        @unlink($path);
    }

    public function test_send_sets_correct_headers(): void
    {
        Http::fake([
            'https://api.sendgrid.com/v3/mail/send' => function ($request) {
                $this->assertStringStartsWith('Bearer SG.', $request->header('Authorization')[0]);
                $this->assertEquals('application/json', $request->header('Content-Type')[0]);

                return Http::response([], 202);
            },
        ]);

        $this->provider->send('from@test.com', 'to@test.com', 'Test', 'Body');
    }

    public function test_send_failure_throws_exception(): void
    {
        Http::fake([
            'https://api.sendgrid.com/v3/mail/send' => function () {
                throw new \Exception('Connection failed');
            },
        ]);

        $result = $this->provider->send('from@test.com', 'to@test.com', 'Test', 'Body');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Connection failed', $result->error ?? '');
    }
}
