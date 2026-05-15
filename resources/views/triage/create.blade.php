@extends('layouts.adminlte', ['title' => 'Nova Triagem'])
@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('triage.store') }}">@csrf
            <div class="form-group"><label>Pet</label><select name="pet_id" class="form-control" required>
                <option value="">Selecione</option>
                @foreach($pets as $pet)<option value="{{ $pet->id }}">{{ $pet->name }} - {{ optional($pet->tutors->first())->name }}</option>@endforeach
            </select></div>
            <div class="form-group"><label>Severidade</label>
                <select name="severity" class="form-control" required>
                    <option value="green">Verde - Não urgente</option>
                    <option value="yellow">Amarela - Prioritário</option>
                    <option value="orange">Laranja - Urgência</option>
                    <option value="red">Vermelho - Emergência</option>
                </select></div>
            <div class="form-group"><label>Queixa Principal</label><textarea name="chief_complaint" class="form-control" required></textarea></div>
            <button type="submit" class="btn btn-primary">Iniciar Triagem</button>
        </form>
    </div></div>
@endsection
