@extends('layouts.adminlte', ['title' => 'Personalização'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-paint-brush mr-2"></i>Personalização</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('configuracoes.branding.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Geral --}}
                        <h6 class="text-primary"><i class="fas fa-info-circle mr-1"></i>Geral</h6>
                        <hr>

                        <div class="form-group">
                            <label>Nome da Clínica</label>
                            <input type="text" name="clinic_name" class="form-control" value="{{ branding('clinic_name', 'VetEssence') }}" placeholder="VetEssence">
                            <small class="text-muted">Exibido no título, sidebar, navbar e documentos.</small>
                        </div>

                        <div class="form-group">
                            <label>Logotipo</label>
                            <div>
                                @php $logo = branding_logo_url(); @endphp
                                @if($logo && !str_contains($logo, 'logo-default.png'))
                                    <img src="{{ $logo }}" alt="Logo" class="img-fluid mb-2" style="max-height: 80px;">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="remove_logo" value="1" class="form-check-input" id="remove_logo">
                                        <label class="form-check-label text-danger" for="remove_logo">Remover logo</label>
                                    </div>
                                @else
                                    <p class="text-muted">Nenhum logo personalizado.</p>
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" name="logo" class="custom-file-input" id="logo" accept="image/png,image/jpeg,image/svg+xml">
                                <label class="custom-file-label" for="logo">PNG, JPG ou SVG (max 2MB)</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Favicon</label>
                            <div>
                                @php $favicon = branding_favicon_url(); @endphp
                                @if($favicon && !str_contains($favicon, 'favicon.ico'))
                                    <div class="mb-2">
                                        <img src="{{ $favicon }}" alt="Favicon" style="max-height: 32px;">
                                        <div class="form-check mt-1">
                                            <input type="checkbox" name="remove_favicon" value="1" class="form-check-input" id="remove_favicon">
                                            <label class="form-check-label text-danger" for="remove_favicon">Remover favicon</label>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted">Nenhum favicon personalizado.</p>
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" name="favicon" class="custom-file-input" id="favicon" accept="image/png,image/x-icon">
                                <label class="custom-file-label" for="favicon">PNG ou ICO (max 1MB)</label>
                            </div>
                        </div>

                        {{-- Exibição do Nome --}}
                        <h6 class="text-primary mt-4"><i class="fas fa-font mr-1"></i>Exibição do Nome</h6>
                        <hr>

                        @php
                            $showName = branding('show_clinic_name', '1');
                            $position = branding('clinic_name_position', 'right');
                        @endphp

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="show_clinic_name" value="1" class="custom-control-input" id="show_clinic_name" {{ $showName === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="show_clinic_name">Exibir nome da clínica ao lado do logo</label>
                            </div>
                        </div>

                        <div class="form-group" id="positionGroup">
                            <label>Posição do nome em relação ao logo</label>
                            <div class="d-flex flex-wrap gap-3 mt-2">
                                @foreach(['above' => 'Acima', 'below' => 'Abaixo', 'left' => 'Esquerda', 'right' => 'Direita'] as $val => $label)
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="clinic_name_position" value="{{ $val }}" class="form-check-input" id="pos_{{ $val }}" {{ $position === $val ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pos_{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div class="border rounded p-3 bg-light mb-3" id="previewName">
                            <small class="text-muted d-block mb-2">Pré-visualização (sidebar):</small>
                            <div class="d-inline-flex align-items-center p-2 rounded text-white font-weight-bold" style="background: {{ branding('primary_color', '#4f46e5') }};" id="previewBrand">
                                <img src="{{ $logo && !str_contains($logo, 'logo-default.png') ? $logo : 'https://via.placeholder.com/40' }}" width="{{ branding('sidebar_logo_width', 40) }}" class="mr-2" id="previewLogo">
                                <span id="previewNameText" style="{{ $showName !== '1' ? 'display:none' : '' }}">{{ branding('clinic_name', 'VetEssence') }}</span>
                            </div>
                        </div>

                        {{-- Cores --}}
                        <h6 class="text-primary mt-4"><i class="fas fa-palette mr-1"></i>Cores</h6>
                        <hr>

                        <div class="form-group">
                            <label>Cor Primária</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background: {{ branding('primary_color', '#4f46e5') }}; width: 38px;" id="primaryColorPreview"></span>
                                </div>
                                <input type="color" name="primary_color" class="form-control" value="{{ branding('primary_color', '#4f46e5') }}" style="padding: 2px;" id="primaryColor">
                            </div>
                            <small class="text-muted">Usada na sidebar, botões e elementos principais.</small>
                        </div>

                        <div class="form-group">
                            <label>Cor de Fundo da Tela de Login</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background: {{ branding('login_background', '#f3f4f6') }}; width: 38px;" id="loginBgPreview"></span>
                                </div>
                                <input type="color" name="login_background" class="form-control" value="{{ branding('login_background', '#f3f4f6') }}" style="padding: 2px;" id="loginBg">
                            </div>
                        </div>

                        <div class="border rounded p-3 bg-light mb-3">
                            <small class="text-muted d-block mb-2">Pré-visualização de cores:</small>
                            <button class="btn text-white mr-2" style="background: {{ branding('primary_color', '#4f46e5') }};" id="previewBtn">
                                <i class="fas fa-check mr-1"></i>Botão Exemplo
                            </button>
                            <a href="#" class="btn btn-outline-secondary">Secundário</a>
                        </div>

                        {{-- Ajustes --}}
                        <h6 class="text-primary mt-4"><i class="fas fa-sliders-h mr-1"></i>Ajustes</h6>
                        <hr>

                        <div class="form-group">
                            <label>Largura do logo no sidebar (px)</label>
                            <input type="number" name="sidebar_logo_width" class="form-control" value="{{ branding('sidebar_logo_width', 40) }}" min="20" max="200" id="sidebarLogoWidth">
                            <small class="text-muted">Apenas se um logo personalizado estiver definido.</small>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Salvar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
#previewName .d-inline-flex { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
</style>
@endpush

@push('scripts')
<script>
document.getElementById('logo')?.addEventListener('change', function(e) {
    var label = this.nextElementSibling;
    if (label) label.textContent = e.target.files[0]?.name || 'Nenhum arquivo';
});
document.getElementById('favicon')?.addEventListener('change', function(e) {
    var label = this.nextElementSibling;
    if (label) label.textContent = e.target.files[0]?.name || 'Nenhum arquivo';
});

// Preview: toggle name visibility
document.getElementById('show_clinic_name')?.addEventListener('change', function() {
    document.getElementById('previewNameText').style.display = this.checked ? '' : 'none';
});

// Preview: position
document.querySelectorAll('[name="clinic_name_position"]').forEach(function(r) {
    r.addEventListener('change', function() {
        var preview = document.getElementById('previewBrand');
        preview.classList.remove('flex-row', 'flex-col', 'flex-row-reverse', 'flex-col-reverse');
        switch (this.value) {
            case 'above': preview.classList.add('flex-col'); break;
            case 'below': preview.classList.add('flex-col-reverse'); break;
            case 'left': preview.classList.add('flex-row-reverse'); break;
            case 'right': preview.classList.add('flex-row'); break;
        }
    });
});
// Trigger initial position
var checked = document.querySelector('[name="clinic_name_position"]:checked');
if (checked) checked.dispatchEvent(new Event('change'));

// Preview: color updates
document.getElementById('primaryColor')?.addEventListener('input', function() {
    document.getElementById('primaryColorPreview').style.background = this.value;
    document.getElementById('previewBrand').style.background = this.value;
    document.getElementById('previewBtn').style.background = this.value;
});
document.getElementById('loginBg')?.addEventListener('input', function() {
    document.getElementById('loginBgPreview').style.background = this.value;
});

// Preview: logo width
document.getElementById('sidebarLogoWidth')?.addEventListener('input', function() {
    document.getElementById('previewLogo').style.width = this.value + 'px';
});
</script>
@endpush
