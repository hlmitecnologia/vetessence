@extends('layouts.adminlte', ['title' => 'Avaliações Pré-Anestésicas'])
@section('content')
    <div class="card">
        <div class="card-header"><a href="{{ route('pre-anesthetic-evaluations.create') }}" class="btn btn-primary">Nova Avaliação</a></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Pet</th><th>ASA</th><th>Status</th><th>Ações</th></tr></thead>
                <tbody>
                @foreach($evaluations as $e)
                    <tr>
                        <td>{{ $e->pet->name ?? '-' }}</td>
                        <td>{{ $e->asa_score }}</td>
                        <td>{{ $e->status }}</td>
                        <td>
                            <a href="{{ route('pre-anesthetic-evaluations.show', $e) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('pre-anesthetic-evaluations.edit', $e) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $evaluations->links() }}</div>
    </div>
@endsection
