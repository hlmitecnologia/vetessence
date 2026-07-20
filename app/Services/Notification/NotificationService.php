<?php

namespace App\Services\Notification;

use App\Services\Notification\Contracts\EmailProvider;
use App\Services\Notification\Contracts\SmsProvider;
use App\Services\Notification\Contracts\WhatsAppProvider;
use App\Services\Notification\Email\MailerSendProvider;
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
                notification_config('email_from', $from),
                $destination,
                $subject ?? 'Sem assunto',
                $message,
                $attachments
            ),
            NotificationChannel::Sms => $provider->send(
                $from,
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
        $provider = notification_config('email_provider', '');

        return match ($provider) {
            'mailersend' => new MailerSendProvider([
                'api_key' => notification_config('email_mailersend_api_key', ''),
                'from_name' => notification_config('email_from_name', config('app.name')),
            ]),
            'smtp' => new SmtpProvider([
                'host' => notification_config('email_smtp_host', ''),
                'port' => notification_config('email_smtp_port', '587'),
                'username' => notification_config('email_smtp_username', ''),
                'password' => notification_config('email_smtp_password', ''),
                'encryption' => notification_config('email_smtp_encryption', 'tls'),
                'from_name' => notification_config('email_from_name', config('app.name')),
            ]),
            'mailgun' => new MailgunProvider([
                'domain' => notification_config('email_mailgun_domain', ''),
                'secret' => notification_config('email_mailgun_secret', ''),
                'endpoint' => notification_config('email_mailgun_endpoint', 'api.mailgun.net'),
                'from_name' => notification_config('email_from_name', config('app.name')),
            ]),
            'ses' => new SesProvider([
                'key' => notification_config('email_ses_key', ''),
                'secret' => notification_config('email_ses_secret', ''),
                'region' => notification_config('email_ses_region', 'us-east-1'),
                'from_name' => notification_config('email_from_name', config('app.name')),
            ]),
            'sendgrid' => new SendGridProvider([
                'api_key' => notification_config('email_sendgrid_api_key', ''),
                'from_name' => notification_config('email_from_name', config('app.name')),
            ]),
            default => null,
        };
    }

    public function resolveSmsProvider(): ?SmsProvider
    {
        $provider = notification_config('sms_provider', '');

        return match ($provider) {
            'twilio' => new TwilioSmsProvider([
                'account_sid' => notification_config('sms_twilio_account_sid', ''),
                'auth_token' => notification_config('sms_twilio_auth_token', ''),
                'from_number' => notification_config('sms_twilio_from_number', ''),
            ]),
            'zenvio' => new ZenvioSmsProvider([
                'api_key' => notification_config('sms_zenvio_api_key', ''),
                'from_number' => notification_config('sms_zenvio_from_number', ''),
            ]),
            'sns' => new SnsSmsProvider([
                'key' => notification_config('sms_sns_key', ''),
                'secret' => notification_config('sms_sns_secret', ''),
                'region' => notification_config('sms_sns_region', 'us-east-1'),
            ]),
            default => null,
        };
    }

    public function resolveWhatsAppProvider(): ?WhatsAppProvider
    {
        $provider = notification_config('whatsapp_provider', '');

        return match ($provider) {
            'zapi' => new ZapiProvider([
                'api_url' => notification_config('whatsapp_zapi_url', ''),
                'api_token' => notification_config('whatsapp_zapi_token', ''),
                'instance' => notification_config('whatsapp_zapi_instance', ''),
            ]),
            'weni' => new WeniProvider([
                'api_key' => notification_config('whatsapp_weni_api_key', ''),
                'project_uuid' => notification_config('whatsapp_weni_project_uuid', ''),
                'from_number' => notification_config('whatsapp_weni_from_number', ''),
            ]),
            'cloudapi' => new CloudApiProvider([
                'access_token' => notification_config('whatsapp_cloudapi_access_token', ''),
                'phone_number_id' => notification_config('whatsapp_cloudapi_phone_number_id', ''),
            ]),
            'twilio' => new TwilioWhatsAppProvider([
                'account_sid' => notification_config('whatsapp_twilio_account_sid', ''),
                'auth_token' => notification_config('whatsapp_twilio_auth_token', ''),
                'from_number' => notification_config('whatsapp_twilio_from_number', ''),
            ]),
            default => null,
        };
    }
}
