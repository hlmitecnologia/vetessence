<?php

namespace App\Services\Notification;

use App\Services\Notification\Contracts\EmailProvider;
use App\Services\Notification\Contracts\SmsProvider;
use App\Services\Notification\Contracts\WhatsAppProvider;
use App\Services\Notification\Email\MailgunProvider;
use App\Services\Notification\Email\SendGridProvider;
use App\Services\Notification\Email\SesProvider;
use App\Services\Notification\Email\SmtpProvider;
use App\Services\Notification\Sms\SnsSmsProvider;
use App\Services\Notification\Sms\TwilioSmsProvider;
use App\Services\Notification\Sms\ZenvioSmsProvider;
use App\Services\Notification\WhatsApp\CloudApiProvider;
use App\Services\Notification\WhatsApp\TwilioWhatsAppProvider;
use App\Services\Notification\WhatsApp\WeniProvider;
use App\Services\Notification\WhatsApp\ZapiProvider;

class NotificationService
{
    public function send(NotificationChannel $channel, string $destination, string $message, ?string $subject = null, array $attachments = []): NotificationResult
    {
        $provider = match ($channel) {
            NotificationChannel::Email => $this->resolveEmailProvider(),
            NotificationChannel::Sms => $this->resolveSmsProvider(),
            NotificationChannel::WhatsApp => $this->resolveWhatsAppProvider(),
        };

        if (!$provider) {
            return NotificationResult::failed('none', "No provider configured for {$channel->value}");
        }

        $from = branding('clinic_name', config('app.name'));

        return match ($channel) {
            NotificationChannel::Email => $provider->send(
                branding('notification_email_from', $from),
                $destination,
                $subject ?? 'Sem assunto',
                $message,
                $attachments
            ),
            NotificationChannel::Sms => $provider->send(
                branding('notification_sms_from', $from),
                $destination,
                $message
            ),
            NotificationChannel::WhatsApp => $provider->send(
                $from,
                $destination,
                $message
            ),
        };
    }

    public function resolveEmailProvider(): ?EmailProvider
    {
        $provider = branding('notification_email_provider', '');

        return match ($provider) {
            'smtp' => new SmtpProvider([
                'host' => branding('notification_email_smtp_host', ''),
                'port' => branding('notification_email_smtp_port', '587'),
                'username' => branding('notification_email_smtp_username', ''),
                'password' => branding('notification_email_smtp_password', ''),
                'encryption' => branding('notification_email_smtp_encryption', 'tls'),
                'from_name' => branding('notification_email_from_name', config('app.name')),
            ]),
            'mailgun' => new MailgunProvider([
                'domain' => branding('notification_email_mailgun_domain', ''),
                'secret' => branding('notification_email_mailgun_secret', ''),
                'endpoint' => branding('notification_email_mailgun_endpoint', 'api.mailgun.net'),
                'from_name' => branding('notification_email_from_name', config('app.name')),
            ]),
            'ses' => new SesProvider([
                'key' => branding('notification_email_ses_key', ''),
                'secret' => branding('notification_email_ses_secret', ''),
                'region' => branding('notification_email_ses_region', 'us-east-1'),
                'from_name' => branding('notification_email_from_name', config('app.name')),
            ]),
            'sendgrid' => new SendGridProvider([
                'api_key' => branding('notification_email_sendgrid_api_key', ''),
                'from_name' => branding('notification_email_from_name', config('app.name')),
            ]),
            default => null,
        };
    }

    public function resolveSmsProvider(): ?SmsProvider
    {
        $provider = branding('notification_sms_provider', '');

        return match ($provider) {
            'twilio' => new TwilioSmsProvider([
                'account_sid' => branding('notification_sms_twilio_account_sid', ''),
                'auth_token' => branding('notification_sms_twilio_auth_token', ''),
                'from_number' => branding('notification_sms_twilio_from_number', ''),
            ]),
            'zenvio' => new ZenvioSmsProvider([
                'api_key' => branding('notification_sms_zenvio_api_key', ''),
                'from_number' => branding('notification_sms_zenvio_from_number', ''),
            ]),
            'sns' => new SnsSmsProvider([
                'key' => branding('notification_sms_sns_key', ''),
                'secret' => branding('notification_sms_sns_secret', ''),
                'region' => branding('notification_sms_sns_region', 'us-east-1'),
            ]),
            default => null,
        };
    }

    public function resolveWhatsAppProvider(): ?WhatsAppProvider
    {
        $provider = branding('notification_whatsapp_provider', '');

        return match ($provider) {
            'zapi' => new ZapiProvider([
                'api_url' => branding('notification_whatsapp_zapi_url', ''),
                'api_token' => branding('notification_whatsapp_zapi_token', ''),
                'instance' => branding('notification_whatsapp_zapi_instance', ''),
            ]),
            'weni' => new WeniProvider([
                'api_key' => branding('notification_whatsapp_weni_api_key', ''),
                'project_uuid' => branding('notification_whatsapp_weni_project_uuid', ''),
                'from_number' => branding('notification_whatsapp_weni_from_number', ''),
            ]),
            'cloudapi' => new CloudApiProvider([
                'access_token' => branding('notification_whatsapp_cloudapi_access_token', ''),
                'phone_number_id' => branding('notification_whatsapp_cloudapi_phone_number_id', ''),
            ]),
            'twilio' => new TwilioWhatsAppProvider([
                'account_sid' => branding('notification_whatsapp_twilio_account_sid', ''),
                'auth_token' => branding('notification_whatsapp_twilio_auth_token', ''),
                'from_number' => branding('notification_whatsapp_twilio_from_number', ''),
            ]),
            default => null,
        };
    }
}
