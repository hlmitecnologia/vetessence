<?php

namespace Tests\Unit\Services\Notification;

use App\Services\Notification\NotificationResult;
use Tests\ModuleTestCase;

class NotificationResultTest extends ModuleTestCase
{
    public function test_success_result(): void
    {
        $result = NotificationResult::success('Mailgun', 'msg-123');

        $this->assertTrue($result->success);
        $this->assertEquals('Mailgun', $result->provider);
        $this->assertEquals('msg-123', $result->messageId);
        $this->assertNull($result->error);
    }

    public function test_success_result_without_message_id(): void
    {
        $result = NotificationResult::success('SendGrid');

        $this->assertTrue($result->success);
        $this->assertEquals('SendGrid', $result->provider);
        $this->assertNull($result->messageId);
        $this->assertNull($result->error);
    }

    public function test_failed_result(): void
    {
        $result = NotificationResult::failed('Mailgun', 'timeout');

        $this->assertFalse($result->success);
        $this->assertEquals('Mailgun', $result->provider);
        $this->assertNull($result->messageId);
        $this->assertEquals('timeout', $result->error);
    }

    public function test_properties_are_readonly(): void
    {
        $reflection = new \ReflectionClass(NotificationResult::class);

        foreach (['success', 'provider', 'messageId', 'error'] as $property) {
            $reflectionProp = $reflection->getProperty($property);
            $this->assertTrue($reflectionProp->isReadOnly());
        }
    }

    public function test_constructor_sets_all_properties(): void
    {
        $result = new NotificationResult(true, 'SES', 'ses-1', 'error-msg');

        $this->assertTrue($result->success);
        $this->assertEquals('SES', $result->provider);
        $this->assertEquals('ses-1', $result->messageId);
        $this->assertEquals('error-msg', $result->error);
    }
}
