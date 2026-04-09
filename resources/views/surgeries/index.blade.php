@extends('layouts.app', ['title' => 'Cirurgias'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Cirurgias</h2>
        <a href="{{ route('surgeries.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Nova Cirurgia
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
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Agendada</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Realizada</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Veterinário</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($surgeries as $surgery)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ $surgery->scheduled_date->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 font-medium">{{ $surgery->pet->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $surgery->surgery_type }}</td>
                    <td class="px-6 py-4 text-sm">{{ $surgery->vet->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @php $statusClass = match($surgery->status) { 'scheduled' => 'bg-blue-100 text-blue-800', 'pre_op' => 'bg-yellow-100 text-yellow-800', 'in_progress' => 'bg-red-100 text-red-800', 'post_op' => 'bg-purple-100 text-purple-800', 'completed' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-gray-100 text-gray-800', default => 'bg-gray-100' }; @endphp
                        @php $statusLabels = ['scheduled' => 'Agendada', 'pre_op' => 'Pré-op', 'in_progress' => 'Em Andamento', 'post_op' => 'Pós-op', 'completed' => 'Realizada', 'cancelled' => 'Cancelada']; @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">{{ $statusLabels[$surgery->status] ?? $surgery->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('surgeries.show', $surgery) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('surgeries.edit', $surgery) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Nenhuma cirurgia encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $surgeries->links() }}</div>
</div>
@endsection
