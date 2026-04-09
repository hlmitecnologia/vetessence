@extends('layouts.adminlte', ['title' => 'Exames'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Exames</h2>
        <a href="{{ route('exams.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Solicitar Exame
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4">
            <select name="pet_id" class="px-4 py-2 border rounded-lg">
                <option value="">Todos os Pets</option>
                @foreach($pets as $pet)
                <option value="{{ $pet->id }}" {{ request('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">Todos Status</option>
                <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Solicitado</option>
                <option value="collected" {{ request('status') == 'collected' ? 'selected' : '' }}>Coletado</option>
                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Pronto</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Solicitação</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($exams as $exam)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $exam->pet->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $exam->type }}</td>
                    <td class="px-6 py-4 text-sm">{{ $exam->requested_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @php $statusClass = match($exam->status) { 'requested' => 'bg-yellow-100 text-yellow-800', 'collected' => 'bg-blue-100 text-blue-800', 'analyzing' => 'bg-purple-100 text-purple-800', 'ready' => 'bg-green-100 text-green-800', 'delivered' => 'bg-gray-100 text-gray-800', default => 'bg-gray-100' }; @endphp
                        @php $statusLabels = ['requested' => 'Solicitado', 'collected' => 'Coletado', 'analyzing' => 'Analisando', 'ready' => 'Pronto', 'delivered' => 'Entregue']; @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">{{ $statusLabels[$exam->status] ?? $exam->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('exams.show', $exam) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('exams.edit', $exam) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Nenhum exame encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $exams->links() }}</div>
</div>
@endsection
