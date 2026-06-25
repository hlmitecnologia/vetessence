@extends('portal.layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="mb-8 portal-fade-in">
    <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-paw" style="color: var(--brand-primary, #455e36)"></i>
        Olá, {{ Auth::guard('tutor')->user()->name }}!
    </h1>
    <p class="text-lg text-gray-500 mt-1">Bem-vindo ao Portal do Tutor</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <a href="{{ route('portal.pets.index') }}" class="portal-card p-6 sm:p-8 hover:shadow-lg transition block portal-fade-in">
        <div class="flex items-center gap-4">
            <div class="portal-icon-wrapper bg-blue-100">
                <i class="fas fa-paw text-blue-600 text-3xl"></i>
            </div>
            <div>
                <p class="text-4xl font-bold text-gray-800">{{ $petsCount }}</p>
                <p class="text-lg text-gray-500">Pets</p>
            </div>
        </div>
    </a>

    <a href="{{ route('portal.appointments.index') }}" class="portal-card p-6 sm:p-8 hover:shadow-lg transition block portal-fade-in">
        <div class="flex items-center gap-4">
            <div class="portal-icon-wrapper bg-green-100">
                <i class="fas fa-calendar-check text-green-600 text-3xl"></i>
            </div>
            <div>
                <p class="text-4xl font-bold text-gray-800">{{ $upcomingAppointments }}</p>
                <p class="text-lg text-gray-500">Próximas consultas</p>
            </div>
        </div>
    </a>

    <a href="{{ route('portal.invoices.index') }}" class="portal-card p-6 sm:p-8 hover:shadow-lg transition block portal-fade-in">
        <div class="flex items-center gap-4">
            <div class="portal-icon-wrapper bg-yellow-100">
                <i class="fas fa-file-invoice text-yellow-600 text-3xl"></i>
            </div>
            <div>
                <p class="text-4xl font-bold text-gray-800">{{ $pendingInvoices }}</p>
                <p class="text-lg text-gray-500">Faturas pendentes</p>
            </div>
        </div>
    </a>

    <a href="{{ route('portal.docs.index') }}" class="portal-card p-6 sm:p-8 hover:shadow-lg transition block portal-fade-in">
        <div class="flex items-center gap-4">
            <div class="portal-icon-wrapper bg-purple-100">
                <i class="fas fa-question-circle text-purple-600 text-3xl"></i>
            </div>
            <div>
                <p class="text-lg font-semibold text-gray-800">Manual do Tutor</p>
                <p class="text-lg text-gray-500">Tire suas dúvidas</p>
            </div>
        </div>
    </a>
</div>

@if($upcomingAppointmentsList->isNotEmpty())
<div class="portal-card p-6 sm:p-8 portal-fade-in">
    <h2 class="portal-section-title">
        <i class="fas fa-calendar-alt"></i>
        Próximas consultas
    </h2>
    <div class="space-y-4">
        @foreach($upcomingAppointmentsList as $appt)
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
            <div class="flex items-center gap-4">
                @if($appt->pet->photo_url)
                <img src="{{ $appt->pet->photo_url }}" alt="{{ $appt->pet->name }}"
                     class="w-14 h-14 rounded-full object-cover border-2 border-gray-200">
                @else
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-paw text-blue-600 text-xl"></i>
                </div>
                @endif
                <div>
                    <p class="text-lg font-medium text-gray-800">{{ $appt->pet->name ?? 'Pet' }}</p>
                    <p class="text-base text-gray-500">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            @php $statusLabels = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Não Compareceu']; @endphp
            <span class="portal-badge {{ $appt->status == 'scheduled' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $statusLabels[$appt->status] ?? $appt->status }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
