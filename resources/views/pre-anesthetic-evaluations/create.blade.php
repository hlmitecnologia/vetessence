@extends('layouts.adminlte', ['title' => 'Nova Avaliação Pré-Anestésica'])
@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('pre-anesthetic-evaluations.store') }}">@csrf
            @include('pre-anesthetic-evaluations._form')
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div></div>
@endsection
