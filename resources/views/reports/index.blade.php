@extends('layouts.adminlte', ['title' => ucfirst('$dir')])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">@if(str_contains('$dir', '-')) {{ str_replace('-', ' ', ucwords('$dir', '-')) }} @else {{ ucfirst('$dir') }} @endif</h2>
        <a href="{{ route('$dir.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <p class="text-gray-500 text-center py-8">Página em desenvolvimento.</p>
</div>
@endsection
