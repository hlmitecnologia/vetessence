@extends('layouts.adminlte', ['title' => 'Editar Vacina'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('vaccinations.update', $vaccination) }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vacina</label>
                                <input type="text" name="vaccine" value="{{ $vaccination->vaccine }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data</label>
                                <input type="date" name="date" value="{{ $vaccination->date->format('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Próxima Dose</label>
                                <input type="date" name="next_date" value="{{ $vaccination->next_date ? $vaccination->next_date->format('Y-m-d') : '' }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Veterinário</label>
                                <select name="vet_id" class="form-control">
                                    @foreach($veterinarians as $vet)
                                    <option value="{{ $vet->id }}" {{ $vaccination->vet_id == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="notes" rows="2" class="form-control">{{ $vaccination->notes }}</textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('vaccinations.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
