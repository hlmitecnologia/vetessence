@if($vitalSigns->count() > 0)
<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead class="thead-dark">
            <tr>
                <th>Horário</th>
                <th>FC (bpm)</th>
                <th>FR (mpm)</th>
                <th>SpO₂ (%)</th>
                <th>ETCO₂ (mmHg)</th>
                <th>PAS (mmHg)</th>
                <th>PAD (mmHg)</th>
                <th>PAM (mmHg)</th>
                <th>Temperatura (°C)</th>
                <th>Plano</th>
                <th>Vaporizador (%)</th>
                <th>Observações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vitalSigns->sortBy('recorded_at') as $vs)
            <tr>
                <td class="font-weight-bold">{{ $vs->recorded_at->format('H:i') }}</td>
                <td class="{{ $vs->heart_rate && ($vs->heart_rate < 60 || $vs->heart_rate > 180) ? 'text-danger font-weight-bold' : '' }}">
                    {{ $vs->heart_rate ?? '-' }}
                </td>
                <td class="{{ $vs->respiratory_rate && ($vs->respiratory_rate < 10 || $vs->respiratory_rate > 40) ? 'text-danger font-weight-bold' : '' }}">
                    {{ $vs->respiratory_rate ?? '-' }}
                </td>
                <td class="{{ $vs->spo2 !== null && $vs->spo2 < 95 ? 'text-danger font-weight-bold' : ($vs->spo2 !== null && $vs->spo2 < 97 ? 'text-warning font-weight-bold' : '') }}">
                    {{ $vs->spo2 !== null ? $vs->spo2 . '%' : '-' }}
                </td>
                <td class="{{ $vs->etco2 && ($vs->etco2 < 30 || $vs->etco2 > 50) ? 'text-danger font-weight-bold' : '' }}">
                    {{ $vs->etco2 ?? '-' }}
                </td>
                <td class="{{ $vs->blood_pressure_systolic && ($vs->blood_pressure_systolic < 90 || $vs->blood_pressure_systolic > 160) ? 'text-danger font-weight-bold' : '' }}">
                    {{ $vs->blood_pressure_systolic ?? '-' }}
                </td>
                <td>{{ $vs->blood_pressure_diastolic ?? '-' }}</td>
                <td class="{{ $vs->blood_pressure_mean && $vs->blood_pressure_mean < 60 ? 'text-danger font-weight-bold' : '' }}">
                    {{ $vs->blood_pressure_mean ?? '-' }}
                </td>
                <td class="{{ $vs->temperature && ($vs->temperature < 36.5 || $vs->temperature > 39.5) ? 'text-danger font-weight-bold' : '' }}">
                    {{ $vs->temperature ?? '-' }}
                </td>
                <td>
                    @php
                        $depthColors = ['superficial' => 'warning', 'moderado' => 'success', 'profundo' => 'primary', 'muito_profundo' => 'danger'];
                        $depthLabels = ['superficial' => 'Superf.', 'moderado' => 'Mod.', 'profundo' => 'Prof.', 'muito_profundo' => 'M.Prof.'];
                    @endphp
                    @if($vs->anesthetic_depth)
                        <span class="badge badge-{{ $depthColors[$vs->anesthetic_depth] ?? 'secondary' }} badge-sm">
                            {{ $depthLabels[$vs->anesthetic_depth] ?? $vs->anesthetic_depth }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $vs->vaporizer_setting ?? '-' }}</td>
                <td class="text-truncate" style="max-width: 120px;">{{ $vs->observations ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class="text-center text-muted">Nenhum sinal vital registrado.</p>
@endif
