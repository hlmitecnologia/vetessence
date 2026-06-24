@extends('layouts.adminlte', ['title' => 'Planos Alimentares'])
@section('content')
    <div class="card">
        <div class="card-header"><a href="{{ route('diet-plans.create') }}" class="btn btn-primary">Novo Plano</a></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Pet</th><th>Tipo</th><th>Marca</th><th>Dose Diária</th><th>Ações</th></tr></thead>
                <tbody>
                @foreach($plans as $p)
                    <tr>
                        <td>{{ $p->pet->name ?? '-' }}</td>
                        <td>{{ $p->diet_type }}</td>
                        <td>{{ $p->brand }}</td>
                        <td>{{ $p->daily_amount }}</td>
                        <td>
                            <a href="{{ route('diet-plans.show', $p) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('diet-plans.edit', $p) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
@endsection
