<div>
    <div class="card">
        <div class="card-header"><h5><i class="fas fa-calculator"></i> Calculadora de Dosagem</h5></div>
        <div class="card-body">
            <div class="form-group">
                <label>Espécie</label>
                <select wire:model="species" class="form-control">
                    <option value="">Selecione...</option>
                    @foreach($speciesList as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Fármaco</label>
                <select wire:model="selectedDrugId" class="form-control" {{ !$species ? 'disabled' : '' }}>
                    <option value="">Selecione...</option>
                    @foreach($drugOptions as $d)
                        <option value="{{ $d->id }}">{{ $d->drug }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Peso (kg)</label>
                <input type="number" wire:model="weightKg" class="form-control" step="0.1" min="0.01">
            </div>
            <button wire:click="calculate" class="btn btn-primary"><i class="fas fa-calculator"></i> Calcular</button>

            @if($result)
                <div class="alert alert-success mt-3">
                    <strong>{{ $result['drug'] }}</strong> ({{ $result['species'] }})<br>
                    Peso: {{ $result['weight_kg'] }} kg<br>
                    Dosagem: {{ $result['dosage_mg_kg'] }} mg/kg<br>
                    <h4 class="mt-2">Dose calculada: <strong>{{ $result['calculated_dose_mg'] }} mg</strong></h4>
                    @if($result['max_dose']) <small>Dose máxima: {{ $result['max_dose'] }} mg</small><br> @endif
                    @if($result['route']) <small>Via: {{ $result['route'] }}</small><br> @endif
                    @if($result['notes']) <small>{{ $result['notes'] }}</small> @endif
                </div>
            @endif

            @if($error)
                <div class="alert alert-danger mt-3">{{ $error }}</div>
            @endif
        </div>
    </div>
</div>
