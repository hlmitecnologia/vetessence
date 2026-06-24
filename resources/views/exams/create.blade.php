@extends('layouts.adminlte', ['title' => 'Solicitar Exame'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('exams.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pet *</label>
                        <x-tom-select name="pet_id" :value="old('pet_id', $selectedPet->id ?? '')" required>
                            @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', $selectedPet->id ?? '') == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Exame *</label>
                        <input type="text" name="type" value="{{ old('type') }}" required class="form-control" placeholder="Ex: Hemograma, Raio-X">
                    </div>
                    <div class="form-group">
                        <label>Data de Solicitação *</label>
                        <input type="date" name="requested_date" value="{{ old('requested_date', date('Y-m-d')) }}" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Veterinário Solicitante *</label>
                        <x-tom-select name="vet_id" :value="old('vet_id')" required>
                            @foreach($veterinarians as $vet)
                            <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('exams.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Solicitar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
