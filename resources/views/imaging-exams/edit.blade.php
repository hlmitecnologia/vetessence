@extends('layouts.adminlte', ['title' => 'Editar Exame de Imagem'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Exame - {{ $exam->exam_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('imaging-exams.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('imaging-exams.update', $exam) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nº do Exame</label>
                        <input type="text" class="form-control" value="{{ $exam->exam_number }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exam_type">Tipo *</label>
                        <select name="exam_type" id="exam_type" class="form-control" required>
                            @foreach(['xray' => 'Raio-X', 'ultrasound' => 'Ultrassom', 'ct' => 'Tomografia', 'mri' => 'Ressonância', 'ecg' => 'Eletrocardiograma', 'endoscopy' => 'Endoscopia', 'other' => 'Outro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('exam_type', $exam->exam_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(['scheduled' => 'Agendado', 'performed' => 'Realizado', 'reported' => 'Laudado', 'cancelled' => 'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $exam->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="region">Região</label>
                        <input type="text" name="region" id="region" class="form-control" value="{{ old('region', $exam->region) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exam_date">Data do Exame *</label>
                        <input type="date" name="exam_date" id="exam_date" class="form-control" value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="radiologist_id">Radiologista</label>
                        <x-tom-select name="radiologist_id" id="radiologist_id" :value="old('radiologist_id', $exam->radiologist_id)">
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('radiologist_id', $exam->radiologist_id) == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="findings">Achados</label>
                <textarea name="findings" id="findings" rows="4" class="wysiwyg form-control">{{ old('findings', $exam->findings) }}</textarea>
            </div>
            <div class="form-group">
                <label for="impression">Impressão</label>
                <textarea name="impression" id="impression" rows="3" class="wysiwyg form-control">{{ old('impression', $exam->impression) }}</textarea>
            </div>
            <div class="form-group">
                <label for="recommendations">Recomendações</label>
                <textarea name="recommendations" id="recommendations" rows="3" class="wysiwyg form-control">{{ old('recommendations', $exam->recommendations) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
