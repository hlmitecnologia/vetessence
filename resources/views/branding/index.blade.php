@extends('layouts.adminlte', ['title' => 'Identidade Visual'])

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
                    <h5><i class="fas fa-paint-brush mr-2"></i>Identidade Visual</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('branding.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Nome da Clínica</label>
                            <input type="text" name="clinic_name" class="form-control" value="{{ branding('clinic_name', 'VetEssence') }}" placeholder="VetEssence">
                            <small class="text-muted">Exibido no título, sidebar e documentos.</small>
                        </div>

                        <div class="form-group">
                            <label>Cor Primária</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background: {{ branding('primary_color', '#4f46e5') }}; width: 38px;"></span>
                                </div>
                                <input type="color" name="primary_color" class="form-control" value="{{ branding('primary_color', '#4f46e5') }}" style="padding: 2px;">
                            </div>
                            <small class="text-muted">Usada na sidebar, botões e elementos principais.</small>
                        </div>

                        <hr>
                        <h6><i class="fas fa-image mr-1"></i>Logotipo</h6>

                        <div class="form-group">
                            <label>Logo atual</label>
                            <div>
                                @php $logo = branding_logo_url(); @endphp
                                @if($logo && !str_contains($logo, 'logo-default.png'))
                                    <img src="{{ $logo }}" alt="Logo" class="img-fluid mb-2" style="max-height: 80px;">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="remove_logo" value="1" class="form-check-input" id="remove_logo">
                                        <label class="form-check-label text-danger" for="remove_logo">Remover logo</label>
                                    </div>
                                @else
                                    <p class="text-muted">Nenhum logo personalizado. (Será usado o nome da clínica.)</p>
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" name="logo" class="custom-file-input" id="logo" accept="image/png,image/jpeg,image/svg+xml">
                                <label class="custom-file-label" for="logo">PNG, JPG ou SVG (max 2MB)</label>
                            </div>
                        </div>

                        <hr>
                        <h6><i class="fas fa-star mr-1"></i>Favicon</h6>

                        <div class="form-group">
                            <label>Favicon atual</label>
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

                        <hr>

                        <h6 class="mb-3">Pré-visualização</h6>
                        <div class="border rounded p-4 bg-light" id="preview">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="p-3 rounded text-white font-weight-bold" style="background: {{ branding('primary_color', '#4f46e5') }};">
                                    <i class="fas fa-paw"></i>
                                    <span class="ml-2">{{ branding('clinic_name', 'VetEssence') }}</span>
                                </div>
                            </div>
                            <button class="btn text-white" style="background: {{ branding('primary_color', '#4f46e5') }};">
                                <i class="fas fa-check mr-1"></i>Botão Exemplo
                            </button>
                            <a href="#" class="btn btn-outline-secondary ml-2">Secundário</a>
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
#preview .d-flex { gap: 0.75rem; }
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
</script>
@endpush
