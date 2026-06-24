@extends('layouts.adminlte')

@section('title', 'Plantão - Calendário')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Calendário de Plantão — {{ $start->format('F Y') }}</h3>
        <div>
            <a href="{{ route('staff-schedules.on-call-calendar', ['month' => $start->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-left"></i> Mês anterior
            </a>
            <a href="{{ route('staff-schedules.on-call-calendar', ['month' => now()->format('Y-m')]) }}" class="btn btn-sm btn-outline-primary">
                Hoje
            </a>
            <a href="{{ route('staff-schedules.on-call-calendar', ['month' => $start->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
                Próximo mês <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="12%">Dom</th>
                    <th width="12%">Seg</th>
                    <th width="12%">Ter</th>
                    <th width="12%">Qua</th>
                    <th width="12%">Qui</th>
                    <th width="12%">Sex</th>
                    <th width="12%">Sáb</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $daysInMonth = $start->daysInMonth;
                    $firstDayOfWeek = $start->dayOfWeek;
                    $day = 1;
                @endphp

                @while ($day <= $daysInMonth)
                    <tr>
                        @for ($col = 0; $col < 7; $col++)
                            @if (($day == 1 && $col < $firstDayOfWeek) || $day > $daysInMonth)
                                <td style="height: 100px; vertical-align: top; background: #f8f9fa;"></td>
                            @else
                                @php
                                    $currentDate = $start->copy()->day($day);
                                    $daySchedules = $schedules->get($currentDate->format('Y-m-d'), collect());
                                @endphp
                                <td style="height: 100px; vertical-align: top;">
                                    <div class="font-weight-bold mb-1">{{ $day }}</div>
                                    @foreach ($daySchedules as $schedule)
                                        @php
                                            $onCallType = $schedule->on_call_type;
                                            $icon = match ($onCallType) {
                                                'presencial' => 'fa-user',
                                                'sobreaviso' => 'fa-home',
                                                default => 'fa-phone-alt',
                                            };
                                            $bg = match ($onCallType) {
                                                'presencial' => '#007bff',
                                                'sobreaviso' => '#28a745',
                                                default => '#dc3545',
                                            };
                                        @endphp
                                        <div class="badge d-block mb-1 p-1 text-left" style="background-color: {{ $bg }}; font-size: 11px; white-space: normal; color: #fff;">
                                            <i class="fas {{ $icon }}"></i>
                                            {{ $schedule->user->name }}
                                            <small>({{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }})</small>
                                        </div>
                                    @endforeach
                                </td>
                                @php $day++; @endphp
                            @endif
                        @endfor
                    </tr>
                @endwhile
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Plantões</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Profissional</th>
                    <th>Horário</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $date => $daySchedules)
                    @foreach ($daySchedules as $schedule)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                            <td>{{ $schedule->user->name }}</td>
                            <td>{{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}</td>
                            <td>{{ $schedule->on_call_type ?? 'Plantão' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Nenhum plantão agendado para este mês.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
