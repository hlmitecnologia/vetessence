@extends('layouts.adminlte', ['title' => 'Editar Avaliação Pré-Anestésica'])
@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('pre-anesthetic-evaluations.update', $preAnestheticEvaluation) }}">@csrf @method('PUT')
            @include('pre-anesthetic-evaluations._form')
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div></div>
@endsection
