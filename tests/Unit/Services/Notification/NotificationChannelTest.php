<?php

namespace Tests\Unit\Services\Notification;

use App\Services\Notification\NotificationChannel;
use Tests\ModuleTestCase;

class NotificationChannelTest extends ModuleTestCase
{
    public function test_enum_has_expected_cases(): void
    {
        $cases = NotificationChannel::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(NotificationChannel::Email, $cases);
        $this->assertContains(NotificationChannel::Sms, $cases);
        $this->assertContains(NotificationChannel::WhatsApp, $cases);
    }

    public function test_email_case_value(): void
    {
        $this->assertEquals('email', NotificationChannel::Email->value);
    }

    public function test_sms_case_value(): void
    {
        $this->assertEquals('sms', NotificationChannel::Sms->value);
    }

    public function test_whatsapp_case_value(): void
    {
        $this->assertEquals('whatsapp', NotificationChannel::WhatsApp->value);
    }

    public function test_from_string(): void
    {
        $channel = NotificationChannel::from('email');
        $this->assertSame(NotificationChannel::Email, $channel);
    }

    public function test_try_from_returns_null_for_invalid(): void
    {
        $this->assertNull(NotificationChannel::tryFrom('invalid'));
    }
}
