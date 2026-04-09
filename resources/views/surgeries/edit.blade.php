@extends('layouts.adminlte', ['title' => 'Editar Cirurgia'])

@section('header')
    <a href="{{ route('surgeries.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Cirurgia</h2>
@endsection

@section('content')
<form action="{{ route('surgeries.update', $surgery) }}" method="POST" class="max-w-2xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Data</label><input type="datetime-local" name="scheduled_date" value="{{ \Carbon\Carbon::parse($surgery->scheduled_date)->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label><input type="text" name="surgery_type" value="{{ $surgery->surgery_type }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border rounded-lg">
                    @foreach(['scheduled', 'pre_op', 'in_progress', 'post_op', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ $surgery->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Duração (min)</label><input type="number" name="surgery_duration" value="{{ $surgery->surgery_duration }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Notas Pós-op</label><textarea name="post_op_notes" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ $surgery->post_op_notes }}</textarea></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Complicações</label><textarea name="complications" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ $surgery->complications }}</textarea></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('surgeries.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
