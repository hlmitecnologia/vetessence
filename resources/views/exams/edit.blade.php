@extends('layouts.adminlte', ['title' => 'Editar Exame'])

@section('header')
    <a href="{{ route('exams.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Exame</h2>
@endsection

@section('content')
<form action="{{ route('exams.update', $exam) }}" method="POST" class="max-w-2xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label><input type="text" name="type" value="{{ $exam->type }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border rounded-lg">
                    @foreach(['requested', 'collected', 'analyzing', 'ready', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ $exam->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Data Resultado</label><input type="date" name="result_date" value="{{ $exam->result_date?->format('Y-m-d') }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Laboratório</label><input type="text" name="lab_name" value="{{ $exam->lab_name }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Resultado</label><textarea name="result" rows="4" class="w-full px-4 py-2 border rounded-lg">{{ $exam->result }}</textarea></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Observações</label><textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ $exam->notes }}</textarea></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('exams.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
