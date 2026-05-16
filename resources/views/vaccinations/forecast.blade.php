@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-calendar-alt"></i> Previsão de Vacinas</h4>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="form-inline">
                <label class="mr-2">Período (dias):</label>
                <select name="days" class="form-control mr-3" onchange="this.form.submit()">
                    @foreach([7, 15, 30, 60, 90] as $d)
                        <option value="{{ $d }}" {{ $days == $d ? 'selected' : '' }}>{{ $d }} dias</option>
                    @endforeach
                </select>
                <label class="mr-2">Espécie:</label>
                <select name="species" class="form-control" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    @foreach($speciesList as $s)
                        <option value="{{ $s }}" {{ $species == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead><tr><th>Pet</th><th>Vacina</th><th>Próxima Dose</th><th>Veterinário</th><th></th></tr></thead>
                <tbody>
                    @forelse($vaccinations as $v)
                        <tr>
                            <td><a href="{{ route('pets.show', $v->pet_id) }}">{{ $v->pet->name ?? '-' }}</a></td>
                            <td>{{ $v->vaccine }}</td>
                            <td>{{ $v->next_date->format('d/m/Y') }}</td>
                            <td>{{ $v->vet->name ?? '-' }}</td>
                            <td><a href="{{ route('vaccinations.show', $v) }}" class="btn btn-sm btn-outline-info">Ver</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Nenhuma vacina prevista para este período.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
