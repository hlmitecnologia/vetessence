@extends('layouts.adminlte', ['title' => 'Editar Vacina'])

@section('header')
    <a href="{{ route('vaccinations.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Vacina</h2>
@endsection

@section('content')
<form action="{{ route('vaccinations.update', $vaccination) }}" method="POST" class="max-w-2xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Vacina</label><input type="text" name="vaccine" value="{{ $vaccination->vaccine }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Data</label><input type="date" name="date" value="{{ $vaccination->date->format('Y-m-d') }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Próxima Dose</label><input type="date" name="next_date" value="{{ $vaccination->next_date ? $vaccination->next_date->format('Y-m-d') : '' }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Veterinário</label><select name="vet_id" class="w-full px-4 py-2 border rounded-lg">@foreach($veterinarians as $vet)<option value="{{ $vet->id }}" {{ $vaccination->vet_id == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Observações</label><textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ $vaccination->notes }}</textarea></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('vaccinations.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
