@extends('portal.layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Olá, {{ Auth::guard('tutor')->user()->name }}!</h1>
    <p class="text-gray-500 text-sm">Bem-vindo ao Portal do Tutor</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <a href="{{ route('portal.pets.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-paw text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $petsCount }}</p>
                <p class="text-sm text-gray-500">Pets</p>
            </div>
        </div>
    </a>

    <a href="{{ route('portal.appointments.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-check text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $upcomingAppointments }}</p>
                <p class="text-sm text-gray-500">Próximas consultas</p>
            </div>
        </div>
    </a>

    <a href="{{ route('portal.invoices.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-invoice text-yellow-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingInvoices }}</p>
                <p class="text-sm text-gray-500">Faturas pendentes</p>
            </div>
        </div>
    </a>

    <a href="{{ route('portal.docs.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-question-circle text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-800">Manual do Tutor</p>
                <p class="text-sm text-gray-500">Tire suas dúvidas</p>
            </div>
        </div>
    </a>
</div>

@if($upcomingAppointmentsList->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Próximas consultas</h2>
    <div class="space-y-3">
        @foreach($upcomingAppointmentsList as $appt)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-paw text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $appt->pet->name ?? 'Pet' }}</p>
                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <span class="text-xs px-2 py-1 rounded-full {{ $appt->status == 'scheduled' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $appt->status }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
