@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-pills"></i> Formulario de Farmacos</h4>
        <a href="{{ route('drug-formulary.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo</a>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead><tr><th>Farmaco</th><th>Especie</th><th>mg/kg</th><th>Dose Max</th><th>Via</th><th>Ativo</th><th></th></tr></thead>
                        <tbody>
                            @forelse($formularies as $f)
                            <tr>
                                <td>{{ $f->drug }}</td>
                                <td>{{ $f->species }}</td>
                                <td>{{ $f->dosage_mg_kg }}</td>
                                <td>{{ $f->max_dose ?? '-' }}</td>
                                <td>{{ $f->route ?? '-' }}</td>
                                <td>{!! $f->is_active ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Nao</span>' !!}</td>
                                <td>
                                    <a href="{{ route('drug-formulary.edit', $f) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('drug-formulary.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted">Nenhum farmaco cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $formularies->links() }}
        </div>
        <div class="col-md-4">
            @livewire('dosage-calculator')
        </div>
    </div>
</div>
@endsection
