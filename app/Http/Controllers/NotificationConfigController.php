<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:configuracoes');
    }

    public function index()
    {
        return view('configuracoes.notificacoes.index');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'email_provider' => 'nullable|in:mailersend,smtp,mailgun,ses,sendgrid',
            'email_smtp_host' => 'nullable|string|max:255',
            'email_smtp_port' => 'nullable|string|max:10',
            'email_smtp_username' => 'nullable|string|max:255',
            'email_smtp_password' => 'nullable|string|max:255',
            'email_smtp_encryption' => 'nullable|in:tls,ssl,null',
            'email_from_name' => 'nullable|string|max:255',
            'email_from' => 'nullable|email|max:255',
            'email_mailgun_domain' => 'nullable|string|max:255',
            'email_mailgun_secret' => 'nullable|string|max:255',
            'email_mailgun_endpoint' => 'nullable|string|max:255',
            'email_ses_key' => 'nullable|string|max:255',
            'email_ses_secret' => 'nullable|string|max:255',
            'email_ses_region' => 'nullable|string|max:50',
            'email_sendgrid_api_key' => 'nullable|string|max:255',
            'email_mailersend_api_key' => 'nullable|string|max:255',

            'sms_provider' => 'nullable|in:twilio,zenvio,sns',
            'sms_twilio_account_sid' => 'nullable|string|max:255',
            'sms_twilio_auth_token' => 'nullable|string|max:255',
            'sms_twilio_from_number' => 'nullable|string|max:50',
            'sms_zenvio_api_key' => 'nullable|string|max:255',
            'sms_zenvio_from_number' => 'nullable|string|max:50',
            'sms_sns_key' => 'nullable|string|max:255',
            'sms_sns_secret' => 'nullable|string|max:255',
            'sms_sns_region' => 'nullable|string|max:50',

            'whatsapp_provider' => 'nullable|in:zapi,weni,cloudapi,twilio',
            'whatsapp_zapi_url' => 'nullable|string|max:255',
            'whatsapp_zapi_token' => 'nullable|string|max:255',
            'whatsapp_zapi_instance' => 'nullable|string|max:255',
            'whatsapp_weni_api_key' => 'nullable|string|max:255',
            'whatsapp_weni_project_uuid' => 'nullable|string|max:255',
            'whatsapp_weni_from_number' => 'nullable|string|max:50',
            'whatsapp_cloudapi_access_token' => 'nullable|string|max:500',
            'whatsapp_cloudapi_phone_number_id' => 'nullable|string|max:100',
            'whatsapp_twilio_account_sid' => 'nullable|string|max:255',
            'whatsapp_twilio_auth_token' => 'nullable|string|max:255',
            'whatsapp_twilio_from_number' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set('notification_' . $key, $value ?? '');
        }

        return redirect()->route('configuracoes.notificacoes.index')
            ->with('success', 'Configurações de notificação salvas com sucesso.');
    }
}
