<?php

namespace App\Services\Notification;

enum NotificationChannel: string
{
    case Email = 'email';
    case Sms = 'sms';
    case WhatsApp = 'whatsapp';
}
