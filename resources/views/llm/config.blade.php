@php
    $title = 'Configuração de IA (Diagnóstico)';
@endphp
@extends('layouts.adminlte')

@push('styles')
<style>
.provider-fields { display:none; }
.provider-fields[data-provider="{{ old('provider', $config->provider ?? 'openai') }}"] { display:block; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Configuração de IA para Sugestão de Diagnóstico</h3>
            </div>
            <form action="{{ route('llm.config.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <p class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Configure o provedor de IA para sugerir diagnósticos nos prontuários. A sugestão aparece como um botão no formulário de atendimento — o veterinário decide se usa ou ignora a sugestão.
                    </p>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $config->is_active ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Ativar sugestão de diagnóstico por IA</label>
                        </div>
                    </div>

                    <hr>
                    <h5>Provedor de IA</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provedor *</label>
                                <select name="provider" class="form-control provider-select @error('provider') is-invalid @enderror" data-group="llm" required>
                                    <option value="openai" {{ old('provider', $config->provider ?? 'openai') == 'openai' ? 'selected' : '' }}>OpenAI</option>
                                    <option value="anthropic" {{ old('provider', $config->provider ?? '') == 'anthropic' ? 'selected' : '' }}>Anthropic (Claude)</option>
                                    <option value="gemini" {{ old('provider', $config->provider ?? '') == 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                                    <option value="grok" {{ old('provider', $config->provider ?? '') == 'grok' ? 'selected' : '' }}>Grok (xAI)</option>
                                    <option value="ollama" {{ old('provider', $config->provider ?? '') == 'ollama' ? 'selected' : '' }}>Ollama (Local)</option>
                                </select>
                                @error('provider') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Temperatura</label>
                                <input type="number" step="0.1" min="0" max="2" name="temperature" class="form-control @error('temperature') is-invalid @enderror" value="{{ old('temperature', $config->temperature ?? 0.3) }}">
                                <small class="text-muted">0.0 = preciso, 1.0 = criativo</small>
                                @error('temperature') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Max. Tokens</label>
                                <input type="number" min="100" max="4096" name="max_tokens" class="form-control @error('max_tokens') is-invalid @enderror" value="{{ old('max_tokens', $config->max_tokens ?? 500) }}">
                                @error('max_tokens') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- OPENAI --}}
                    <div class="provider-fields" data-provider="openai" data-group="llm">
                        <h6 class="text-primary mt-3"><i class="fas fa-microchip mr-1"></i>Credenciais OpenAI</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>API Key *</label>
                                    <input type="password" name="openai_api_key" class="form-control @error('openai_api_key') is-invalid @enderror" value="{{ old('openai_api_key', $config->openai_api_key ?? '') }}" placeholder="sk-...">
                                    @error('openai_api_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <input type="text" name="openai_model" class="form-control @error('openai_model') is-invalid @enderror" value="{{ old('openai_model', $config->openai_model ?? 'gpt-4o-mini') }}" placeholder="gpt-4o-mini">
                                    @error('openai_model') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ANTHROPIC --}}
                    <div class="provider-fields" data-provider="anthropic" data-group="llm">
                        <h6 class="text-primary mt-3"><i class="fas fa-robot mr-1"></i>Credenciais Anthropic (Claude)</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>API Key *</label>
                                    <input type="password" name="anthropic_api_key" class="form-control @error('anthropic_api_key') is-invalid @enderror" value="{{ old('anthropic_api_key', $config->anthropic_api_key ?? '') }}" placeholder="sk-ant-...">
                                    @error('anthropic_api_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <input type="text" name="anthropic_model" class="form-control @error('anthropic_model') is-invalid @enderror" value="{{ old('anthropic_model', $config->anthropic_model ?? 'claude-3-haiku-20240307') }}" placeholder="claude-3-haiku-20240307">
                                    @error('anthropic_model') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GEMINI --}}
                    <div class="provider-fields" data-provider="gemini" data-group="llm">
                        <h6 class="text-primary mt-3"><i class="fas fa-cloud mr-1"></i>Credenciais Google Gemini</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>API Key *</label>
                                    <input type="password" name="gemini_api_key" class="form-control @error('gemini_api_key') is-invalid @enderror" value="{{ old('gemini_api_key', $config->gemini_api_key ?? '') }}" placeholder="AIza...">
                                    @error('gemini_api_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <input type="text" name="gemini_model" class="form-control @error('gemini_model') is-invalid @enderror" value="{{ old('gemini_model', $config->gemini_model ?? 'gemini-2.0-flash') }}" placeholder="gemini-2.0-flash">
                                    @error('gemini_model') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GROK --}}
                    <div class="provider-fields" data-provider="grok" data-group="llm">
                        <h6 class="text-primary mt-3"><i class="fas fa-x mr-1"></i>Credenciais Grok (xAI)</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>API Key *</label>
                                    <input type="password" name="grok_api_key" class="form-control @error('grok_api_key') is-invalid @enderror" value="{{ old('grok_api_key', $config->grok_api_key ?? '') }}" placeholder="xai-...">
                                    @error('grok_api_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <input type="text" name="grok_model" class="form-control @error('grok_model') is-invalid @enderror" value="{{ old('grok_model', $config->grok_model ?? 'grok-1') }}" placeholder="grok-1">
                                    @error('grok_model') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- OLLAMA --}}
                    <div class="provider-fields" data-provider="ollama" data-group="llm">
                        <h6 class="text-primary mt-3"><i class="fas fa-server mr-1"></i>Credenciais Ollama (Local)</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Base URL *</label>
                                    <input type="text" name="ollama_base_url" class="form-control @error('ollama_base_url') is-invalid @enderror" value="{{ old('ollama_base_url', $config->ollama_base_url ?? 'http://localhost:11434') }}" placeholder="http://localhost:11434">
                                    @error('ollama_base_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <input type="text" name="ollama_model" class="form-control @error('ollama_model') is-invalid @enderror" value="{{ old('ollama_model', $config->ollama_model ?? 'llama3') }}" placeholder="llama3">
                                    @error('ollama_model') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            Certifique-se de que o servidor Ollama está rodando e acessível.
                        </p>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Configuração</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleLlmProviderFields() {
    var select = document.querySelector('.provider-select[data-group="llm"]');
    if (!select) return;
    var selected = select.value;
    var fields = document.querySelectorAll('.provider-fields[data-group="llm"]');
    for (var i = 0; i < fields.length; i++) {
        var show = fields[i].dataset.provider === selected;
        fields[i].style.display = show ? 'block' : 'none';
        if (show) {
            fields[i].querySelectorAll('.is-invalid').forEach(function(el) {
                el.classList.remove('is-invalid');
            });
            fields[i].querySelectorAll('.invalid-feedback').forEach(function(el) {
                el.style.display = 'none';
            });
        }
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleLlmProviderFields();
    var select = document.querySelector('.provider-select[data-group="llm"]');
    if (select) {
        select.addEventListener('change', toggleLlmProviderFields);
    }
});
</script>
@endpush
