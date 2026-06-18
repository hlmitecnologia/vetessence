@extends('layouts.adminlte', ['title' => 'Editar Integração'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar - {{ $labEquipmentIntegration->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('lab-equipment-integrations.show', $labEquipmentIntegration) }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('lab-equipment-integrations.update', $labEquipmentIntegration) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $labEquipmentIntegration->name) }}" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="equipment_type">Tipo *</label>
                        <select name="equipment_type" class="form-control @error('equipment_type') is-invalid @enderror" required>
                            <option value="hematology" {{ old('equipment_type', $labEquipmentIntegration->equipment_type) == 'hematology' ? 'selected' : '' }}>Hematologia</option>
                            <option value="biochemistry" {{ old('equipment_type', $labEquipmentIntegration->equipment_type) == 'biochemistry' ? 'selected' : '' }}>Bioquímica</option>
                            <option value="urinalysis" {{ old('equipment_type', $labEquipmentIntegration->equipment_type) == 'urinalysis' ? 'selected' : '' }}>Urinálise</option>
                            <option value="immunology" {{ old('equipment_type', $labEquipmentIntegration->equipment_type) == 'immunology' ? 'selected' : '' }}>Imunologia</option>
                            <option value="other" {{ old('equipment_type', $labEquipmentIntegration->equipment_type) == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('equipment_type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="protocol">Protocolo *</label>
                        <select name="protocol" class="form-control @error('protocol') is-invalid @enderror" required>
                            <option value="rest" {{ old('protocol', $labEquipmentIntegration->protocol) == 'rest' ? 'selected' : '' }}>REST API</option>
                            <option value="hl7" {{ old('protocol', $labEquipmentIntegration->protocol) == 'hl7' ? 'selected' : '' }}>HL7</option>
                            <option value="fhir" {{ old('protocol', $labEquipmentIntegration->protocol) == 'fhir' ? 'selected' : '' }}>FHIR</option>
                            <option value="custom" {{ old('protocol', $labEquipmentIntegration->protocol) == 'custom' ? 'selected' : '' }}>Customizado</option>
                        </select>
                        @error('protocol')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="endpoint_url">URL do Endpoint</label>
                        <input type="url" name="endpoint_url" class="form-control" value="{{ old('endpoint_url', $labEquipmentIntegration->endpoint_url) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ip_address">IP</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address', $labEquipmentIntegration->ip_address) }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="port">Porta</label>
                        <input type="number" name="port" class="form-control" value="{{ old('port', $labEquipmentIntegration->port) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="api_key">Chave API</label>
                        <input type="text" name="api_key" class="form-control" value="{{ old('api_key', $labEquipmentIntegration->api_key) }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_active" class="custom-control-input" value="1" {{ old('is_active', $labEquipmentIntegration->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Ativo</label>
                </div>
            </div>
            <div class="form-group">
                <label for="branch_id">Unidade</label>
                <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                    <option value="">Todas as unidades</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $labEquipmentIntegration->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes', $labEquipmentIntegration->notes) }}</textarea>
                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
        </div>
    </form>
</div>
@endsection
