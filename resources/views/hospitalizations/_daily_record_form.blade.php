@php
    $shiftLabels = ['morning' => 'Manhã', 'afternoon' => 'Tarde', 'night' => 'Noite'];
    $appetiteLabels = ['normal' => 'Normal', 'reduced' => 'Reduzido', 'absent' => 'Ausente'];
    $hydrationLabels = ['normal' => 'Normal', 'dehydrated' => 'Desidratado', 'overhydrated' => 'Sobre-hidratado'];
@endphp

<div class="form-group">
    <label for="record_date">Data do Registro *</label>
    <input type="date" name="record_date" id="record_date" class="form-control @error('record_date') is-invalid @enderror"
           value="{{ old('record_date', $dailyRecord->record_date->format('Y-m-d') ?? date('Y-m-d')) }}" required>
    @error('record_date')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="shift">Turno *</label>
    <select name="shift" id="shift" class="form-control @error('shift') is-invalid @enderror" required>
        <option value="">Selecione</option>
        @foreach($shiftLabels as $val => $label)
            <option value="{{ $val }}" {{ old('shift', $dailyRecord->shift ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error('shift')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<h6 class="text-uppercase text-muted mt-4">SOAP</h6>
<hr>
<div class="form-group">
    <label for="subjective">S - Subjetivo</label>
    <textarea name="subjective" id="subjective" rows="2" class="form-control">{{ old('subjective', $dailyRecord->subjective ?? '') }}</textarea>
</div>
<div class="form-group">
    <label for="objective">O - Objetivo</label>
    <textarea name="objective" id="objective" rows="2" class="form-control">{{ old('objective', $dailyRecord->objective ?? '') }}</textarea>
</div>
<div class="form-group">
    <label for="assessment">A - Avaliação</label>
    <textarea name="assessment" id="assessment" rows="2" class="form-control">{{ old('assessment', $dailyRecord->assessment ?? '') }}</textarea>
</div>
<div class="form-group">
    <label for="plan">P - Plano</label>
    <textarea name="plan" id="plan" rows="2" class="form-control">{{ old('plan', $dailyRecord->plan ?? '') }}</textarea>
</div>

<h6 class="text-uppercase text-muted mt-4">Sinais Vitais</h6>
<hr>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="temperature">Temperatura (°C)</label>
            <input type="number" step="0.1" name="temperature" id="temperature" class="form-control" value="{{ old('temperature', $dailyRecord->temperature ?? '') }}" placeholder="Ex: 38.5">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="heart_rate">Frequência Cardíaca (bpm)</label>
            <input type="number" name="heart_rate" id="heart_rate" class="form-control" value="{{ old('heart_rate', $dailyRecord->heart_rate ?? '') }}" placeholder="Ex: 120">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="respiratory_rate">Frequência Respiratória (mpm)</label>
            <input type="number" name="respiratory_rate" id="respiratory_rate" class="form-control" value="{{ old('respiratory_rate', $dailyRecord->respiratory_rate ?? '') }}" placeholder="Ex: 30">
        </div>
    </div>
</div>

<h6 class="text-uppercase text-muted mt-4">Avaliação Clínica</h6>
<hr>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="appetite">Apetite</label>
            <select name="appetite" id="appetite" class="form-control">
                <option value="">Selecione</option>
                @foreach($appetiteLabels as $val => $label)
                    <option value="{{ $val }}" {{ old('appetite', $dailyRecord->appetite ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="hydration">Hidratação</label>
            <select name="hydration" id="hydration" class="form-control">
                <option value="">Selecione</option>
                @foreach($hydrationLabels as $val => $label)
                    <option value="{{ $val }}" {{ old('hydration', $dailyRecord->hydration ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="urination">Urinação</label>
            <select name="urination" id="urination" class="form-control">
                <option value="">Selecione</option>
                <option value="normal" {{ old('urination', $dailyRecord->urination ?? '') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="oliguria" {{ old('urination', $dailyRecord->urination ?? '') == 'oliguria' ? 'selected' : '' }}>Oligúria</option>
                <option value="polyuria" {{ old('urination', $dailyRecord->urination ?? '') == 'polyuria' ? 'selected' : '' }}>Poliúria</option>
                <option value="absent" {{ old('urination', $dailyRecord->urination ?? '') == 'absent' ? 'selected' : '' }}>Ausente</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="defecation">Defecação</label>
            <select name="defecation" id="defecation" class="form-control">
                <option value="">Selecione</option>
                <option value="normal" {{ old('defecation', $dailyRecord->defecation ?? '') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="diarrhea" {{ old('defecation', $dailyRecord->defecation ?? '') == 'diarrhea' ? 'selected' : '' }}>Diarreia</option>
                <option value="constipation" {{ old('defecation', $dailyRecord->defecation ?? '') == 'constipation' ? 'selected' : '' }}>Constipação</option>
                <option value="absent" {{ old('defecation', $dailyRecord->defecation ?? '') == 'absent' ? 'selected' : '' }}>Ausente</option>
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="medications_given">Medicações Administradas</label>
    <textarea name="medications_given" id="medications_given" rows="2" class="form-control">{{ old('medications_given', $dailyRecord->medications_given ?? '') }}</textarea>
</div>
<div class="form-group">
    <label for="observations">Observações</label>
    <textarea name="observations" id="observations" rows="2" class="form-control">{{ old('observations', $dailyRecord->observations ?? '') }}</textarea>
</div>
