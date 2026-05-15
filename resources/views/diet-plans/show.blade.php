@extends('layouts.adminlte', ['title' => 'Plano Alimentar'])
@section('content')
    <div class="card"><div class="card-body">
        <p><strong>Pet:</strong> {{ $dietPlan->pet->name ?? '-' }}</p>
        <p><strong>Tipo:</strong> {{ $dietPlan->diet_type }}</p>
        <p><strong>Marca:</strong> {{ $dietPlan->brand }}</p>
        <p><strong>Produto:</strong> {{ $dietPlan->product_name }}</p>
        <p><strong>Dose Diária:</strong> {{ $dietPlan->daily_amount }}</p>
        <p><strong>Duração:</strong> {{ $dietPlan->duration_days }} dias</p>
        <p><strong>Instruções:</strong> {{ $dietPlan->instructions }}</p>
        <a href="{{ route('diet-plans.edit', $dietPlan) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('diet-plans.index') }}" class="btn btn-secondary">Voltar</a>
    </div></div>
@endsection
