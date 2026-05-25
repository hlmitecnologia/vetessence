@extends('layouts.adminlte', ['title' => 'Notificações'])

@section('css')
<style>
.nav-tabs .nav-link.active {
    color: #fff !important;
    font-weight: 600;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bell mr-2"></i>Configurações de Notificação</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="notificationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="email-tab" data-toggle="tab" href="#email" role="tab">
                                <i class="fas fa-envelope mr-1"></i> E-mail
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="sms-tab" data-toggle="tab" href="#sms" role="tab">
                                <i class="fas fa-sms mr-1"></i> SMS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab">
                                <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                            </a>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('configuracoes.notificacoes.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="tab-content mt-3" id="notificationTabsContent">
                            {{-- TAB EMAIL --}}
                            <div class="tab-pane fade show active" id="email" role="tabpanel">
                                <div class="form-group">
                                    <label>Provedor de E-mail</label>
                                    <select name="email_provider" class="form-control provider-select" data-group="email">
                                        <option value="">-- Selecione --</option>
                                        <option value="smtp" {{ notification_config('email_provider') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="mailgun" {{ notification_config('email_provider') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="ses" {{ notification_config('email_provider') === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                        <option value="sendgrid" {{ notification_config('email_provider') === 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>E-mail remetente</label>
                                    <input type="email" name="email_from" class="form-control" value="{{ notification_config('email_from', '') }}" placeholder="noreply@exemplo.com">
                                </div>

                                <div class="form-group">
                                    <label>Nome do remetente</label>
                                    <input type="text" name="email_from_name" class="form-control" value="{{ notification_config('email_from_name', '') }}" placeholder="Clínica Veterinária">
                                </div>

                                {{-- SMTP --}}
                                <div class="provider-fields" data-provider="smtp" data-group="email" style="display:none;">
                                    <h6 class="text-primary mt-3"><i class="fas fa-server mr-1"></i>SMTP</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Servidor</label>
                                                <input type="text" name="email_smtp_host" class="form-control" value="{{ notification_config('email_smtp_host', '') }}" placeholder="smtp.exemplo.com">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Porta</label>
                                                <input type="text" name="email_smtp_port" class="form-control" value="{{ notification_config('email_smtp_port', '587') }}" placeholder="587">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Criptografia</label>
                                                <select name="email_smtp_encryption" class="form-control">
                                                    <option value="tls" {{ notification_config('email_smtp_encryption', 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                                    <option value="ssl" {{ notification_config('email_smtp_encryption', 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                                    <option value="null" {{ notification_config('email_smtp_encryption', 'tls') === 'null' ? 'selected' : '' }}>Nenhuma</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Usuário</label>
                                                <input type="text" name="email_smtp_username" class="form-control" value="{{ notification_config('email_smtp_username', '') }}" placeholder="seu@email.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Senha</label>
                                                <input type="password" name="email_smtp_password" class="form-control" value="{{ notification_config('email_smtp_password', '') }}" placeholder="********">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mailgun --}}
                                <div class="provider-fields" data-provider="mailgun" data-group="email" style="display:none;">
                                    <h6 class="text-primary mt-3"><i class="fas fa-bullseye mr-1"></i>Mailgun</h6>
                                    <div class="form-group">
                                        <label>Domínio</label>
                                        <input type="text" name="email_mailgun_domain" class="form-control" value="{{ notification_config('email_mailgun_domain', '') }}" placeholder="mg.exemplo.com">
                                    </div>
                                    <div class="form-group">
                                        <label>API Secret</label>
                                        <input type="password" name="email_mailgun_secret" class="form-control" value="{{ notification_config('email_mailgun_secret', '') }}" placeholder="********">
                                    </div>
                                    <div class="form-group">
                                        <label>Endpoint</label>
                                        <select name="email_mailgun_endpoint" class="form-control">
                                            <option value="api.mailgun.net" {{ notification_config('email_mailgun_endpoint', 'api.mailgun.net') === 'api.mailgun.net' ? 'selected' : '' }}>api.mailgun.net (EUA)</option>
                                            <option value="api.eu.mailgun.net" {{ notification_config('email_mailgun_endpoint', 'api.mailgun.net') === 'api.eu.mailgun.net' ? 'selected' : '' }}>api.eu.mailgun.net (Europa)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- SES --}}
                                <div class="provider-fields" data-provider="ses" data-group="email" style="display:none;">
                                    <h6 class="text-primary mt-3"><i class="fas fa-cloud mr-1"></i>Amazon SES</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Access Key ID</label>
                                                <input type="text" name="email_ses_key" class="form-control" value="{{ notification_config('email_ses_key', '') }}" placeholder="AKIA...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Secret Access Key</label>
                                                <input type="password" name="email_ses_secret" class="form-control" value="{{ notification_config('email_ses_secret', '') }}" placeholder="********">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Região</label>
                                        <select name="email_ses_region" class="form-control">
                                            <option value="us-east-1" {{ notification_config('email_ses_region', 'us-east-1') === 'us-east-1' ? 'selected' : '' }}>US East (N. Virginia)</option>
                                            <option value="us-west-2" {{ notification_config('email_ses_region', 'us-east-1') === 'us-west-2' ? 'selected' : '' }}>US West (Oregon)</option>
                                            <option value="sa-east-1" {{ notification_config('email_ses_region', 'us-east-1') === 'sa-east-1' ? 'selected' : '' }}>South America (São Paulo)</option>
                                            <option value="eu-west-1" {{ notification_config('email_ses_region', 'us-east-1') === 'eu-west-1' ? 'selected' : '' }}>EU (Ireland)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- SendGrid --}}
                                <div class="provider-fields" data-provider="sendgrid" data-group="email" style="display:none;">
                                    <h6 class="text-primary mt-3"><i class="fas fa-paper-plane mr-1"></i>SendGrid</h6>
                                    <div class="form-group">
                                        <label>API Key</label>
                                        <input type="password" name="email_sendgrid_api_key" class="form-control" value="{{ notification_config('email_sendgrid_api_key', '') }}" placeholder="SG.********">
                                    </div>
                                </div>
                            </div>

                            {{-- TAB SMS --}}
                            <div class="tab-pane fade" id="sms" role="tabpanel">
                                <div class="form-group">
                                    <label>Provedor de SMS</label>
                                    <select name="sms_provider" class="form-control provider-select" data-group="sms">
                                        <option value="">-- Selecione --</option>
                                        <option value="twilio" {{ notification_config('sms_provider') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                                        <option value="zenvio" {{ notification_config('sms_provider') === 'zenvio' ? 'selected' : '' }}>Zenvio</option>
                                        <option value="sns" {{ notification_config('sms_provider') === 'sns' ? 'selected' : '' }}>Amazon SNS</option>
                                    </select>
                                </div>

                                <div class="provider-fields" data-provider="twilio" data-group="sms" style="display:none;">
                                    <h6 class="text-primary mt-3"><i class="fab fa-twilio mr-1"></i>Twilio</h6>
                                    <div class="form-group">
                                        <label>Account SID</label>
                                        <input type="text" name="sms_twilio_account_sid" class="form-control" value="{{ notification_config('sms_twilio_account_sid', '') }}" placeholder="AC...">
                                    </div>
                                    <div class="form-group">
                                        <label>Auth Token</label>
                                        <input type="password" name="sms_twilio_auth_token" class="form-control" value="{{ notification_config('sms_twilio_auth_token', '') }}" placeholder="********">
                                    </div>
                                    <div class="form-group">
                                        <label>Número de origem</label>
                                        <input type="text" name="sms_twilio_from_number" class="form-control" value="{{ notification_config('sms_twilio_from_number', '') }}" placeholder="+5511999999999">
                                    </div>
                                </div>

                                <div class="provider-fields" data-provider="zenvio" data-group="sms" style="display:none;">
                                    <h6 class="text-primary mt-3"><i class="fas fa-comment-dots mr-1"></i>Zenvio</h6>
                                    <div class="form-group">
                                        <label>API Key</label>
                                        <input type="password" name="sms_zenvio_api_key" class="form-control" value="{{ notification_config('sms_zenvio_api_key', '') }}" placeholder="********">
                                    </div>
                                    <div class="form-group">
                                        <label>Número de origem</label>
                                        <input type="text" name="sms_zenvio_from_number" class="form-control" value="{{ notification_config('sms_zenvio_from_number', '') }}" placeholder="+5511999999999">
                                    </div>
                                </div>

                                <div class="provider-fields" data-provider="sns" data-group="sms" style="display:none;">
                                    <h6 class="text-primary mt-3"><i class="fas fa-cloud mr-1"></i>Amazon SNS</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Access Key ID</label>
                                                <input type="text" name="sms_sns_key" class="form-control" value="{{ notification_config('sms_sns_key', '') }}" placeholder="AKIA...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Secret Access Key</label>
                                                <input type="password" name="sms_sns_secret" class="form-control" value="{{ notification_config('sms_sns_secret', '') }}" placeholder="********">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Região</label>
                                        <select name="sms_sns_region" class="form-control">
                                            <option value="us-east-1" {{ notification_config('sms_sns_region', 'us-east-1') === 'us-east-1' ? 'selected' : '' }}>US East (N. Virginia)</option>
                                            <option value="us-west-2" {{ notification_config('sms_sns_region', 'us-east-1') === 'us-west-2' ? 'selected' : '' }}>US West (Oregon)</option>
                                            <option value="sa-east-1" {{ notification_config('sms_sns_region', 'us-east-1') === 'sa-east-1' ? 'selected' : '' }}>South America (São Paulo)</option>
                                            <option value="eu-west-1" {{ notification_config('sms_sns_region', 'us-east-1') === 'eu-west-1' ? 'selected' : '' }}>EU (Ireland)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB WHATSAPP --}}
                            <div class="tab-pane fade" id="whatsapp" role="tabpanel">
                                <div class="form-group">
                                    <label>Provedor de WhatsApp</label>
                                    <select name="whatsapp_provider" class="form-control provider-select" data-group="whatsapp">
                                        <option value="">-- Selecione --</option>
                                        <option value="zapi" {{ notification_config('whatsapp_provider') === 'zapi' ? 'selected' : '' }}>Z-API</option>
                                        <option value="weni" {{ notification_config('whatsapp_provider') === 'weni' ? 'selected' : '' }}>Weni</option>
                                        <option value="cloudapi" {{ notification_config('whatsapp_provider') === 'cloudapi' ? 'selected' : '' }}>WhatsApp Cloud API (Meta)</option>
                                        <option value="twilio" {{ notification_config('whatsapp_provider') === 'twilio' ? 'selected' : '' }}>Twilio WhatsApp</option>
                                    </select>
                                </div>

                                <div class="provider-fields" data-provider="zapi" data-group="whatsapp" style="display:none;">
                                    <h6 class="text-success mt-3"><i class="fas fa-bolt mr-1"></i>Z-API</h6>
                                    <div class="form-group">
                                        <label>API URL</label>
                                        <input type="text" name="whatsapp_zapi_url" class="form-control" value="{{ notification_config('whatsapp_zapi_url', '') }}" placeholder="https://api.z-api.io/v1">
                                    </div>
                                    <div class="form-group">
                                        <label>API Token</label>
                                        <input type="password" name="whatsapp_zapi_token" class="form-control" value="{{ notification_config('whatsapp_zapi_token', '') }}" placeholder="********">
                                    </div>
                                    <div class="form-group">
                                        <label>Instance ID</label>
                                        <input type="text" name="whatsapp_zapi_instance" class="form-control" value="{{ notification_config('whatsapp_zapi_instance', '') }}" placeholder="Instance ID">
                                    </div>
                                </div>

                                <div class="provider-fields" data-provider="weni" data-group="whatsapp" style="display:none;">
                                    <h6 class="text-success mt-3"><i class="fas fa-robot mr-1"></i>Weni</h6>
                                    <div class="form-group">
                                        <label>API Key</label>
                                        <input type="password" name="whatsapp_weni_api_key" class="form-control" value="{{ notification_config('whatsapp_weni_api_key', '') }}" placeholder="********">
                                    </div>
                                    <div class="form-group">
                                        <label>Project UUID</label>
                                        <input type="text" name="whatsapp_weni_project_uuid" class="form-control" value="{{ notification_config('whatsapp_weni_project_uuid', '') }}" placeholder="UUID do projeto">
                                    </div>
                                    <div class="form-group">
                                        <label>Número de origem</label>
                                        <input type="text" name="whatsapp_weni_from_number" class="form-control" value="{{ notification_config('whatsapp_weni_from_number', '') }}" placeholder="+5511999999999">
                                    </div>
                                </div>

                                <div class="provider-fields" data-provider="cloudapi" data-group="whatsapp" style="display:none;">
                                    <h6 class="text-success mt-3"><i class="fab fa-facebook mr-1"></i>WhatsApp Cloud API (Meta)</h6>
                                    <div class="form-group">
                                        <label>Access Token (permanente)</label>
                                        <input type="password" name="whatsapp_cloudapi_access_token" class="form-control" value="{{ notification_config('whatsapp_cloudapi_access_token', '') }}" placeholder="EAAx...ZD">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number ID</label>
                                        <input type="text" name="whatsapp_cloudapi_phone_number_id" class="form-control" value="{{ notification_config('whatsapp_cloudapi_phone_number_id', '') }}" placeholder="ID do número de telefone">
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Crie um aplicativo Meta Business, adicione o produto WhatsApp, gere um token de acesso permanente e copie o Phone Number ID.
                                    </small>
                                </div>

                                <div class="provider-fields" data-provider="twilio" data-group="whatsapp" style="display:none;">
                                    <h6 class="text-success mt-3"><i class="fab fa-twilio mr-1"></i>Twilio WhatsApp</h6>
                                    <div class="form-group">
                                        <label>Account SID</label>
                                        <input type="text" name="whatsapp_twilio_account_sid" class="form-control" value="{{ notification_config('whatsapp_twilio_account_sid', '') }}" placeholder="AC...">
                                    </div>
                                    <div class="form-group">
                                        <label>Auth Token</label>
                                        <input type="password" name="whatsapp_twilio_auth_token" class="form-control" value="{{ notification_config('whatsapp_twilio_auth_token', '') }}" placeholder="********">
                                    </div>
                                    <div class="form-group">
                                        <label>Número de origem (WhatsApp Sandbox/Approved)</label>
                                        <input type="text" name="whatsapp_twilio_from_number" class="form-control" value="{{ notification_config('whatsapp_twilio_from_number', '') }}" placeholder="+14155238886">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Salvar Configurações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function toggleProviderFields(group) {
        const select = document.querySelector(`.provider-select[data-group="${group}"]`);
        if (!select) return;
        const selected = select.value;
        document.querySelectorAll(`.provider-fields[data-group="${group}"]`).forEach(el => {
            el.style.display = el.dataset.provider === selected ? 'block' : 'none';
        });
    }

    document.querySelectorAll('.provider-select').forEach(select => {
        toggleProviderFields(select.dataset.group);
        select.addEventListener('change', function() {
            toggleProviderFields(this.dataset.group);
        });
    });
});
</script>
@endpush
