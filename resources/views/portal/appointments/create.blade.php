@extends('portal.layouts.app', ['title' => 'Agendar Consulta'])

@section('content')
<div class="mb-6">
    <a href="{{ route('portal.appointments.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left mr-1"></i>Consultas
    </a>
</div>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Agendar Consulta</h1>

    <form id="bookingForm" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pet</label>
            <select name="pet_id" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm">
                <option value="">Selecione um pet</option>
                @foreach($pets as $pet)
                <option value="{{ $pet->id }}">{{ $pet->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo da consulta</label>
            <textarea name="reason" rows="3" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data preferida</label>
            <input type="date" name="preferred_date"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
            <select name="preferred_period"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm">
                <option value="">Qualquer</option>
                <option value="morning">Manhã</option>
                <option value="afternoon">Tarde</option>
            </select>
        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
            Solicitar agendamento
        </button>
    </form>

    <p class="text-xs text-gray-400 mt-4 text-center">
        Sua solicitação será analisada pela equipe e confirmada em breve.
    </p>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);

    try {
        const response = await fetch('/api/v1/online-bookings', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: data,
        });

        if (response.ok) {
            window.location.href = '{{ route("portal.appointments.index") }}?success=1';
        } else {
            const err = await response.json();
            alert(err.message || 'Erro ao enviar solicitação.');
        }
    } catch {
        alert('Erro de conexão. Tente novamente.');
    }
});
</script>
@endpush
