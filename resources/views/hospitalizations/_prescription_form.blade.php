<div class="form-group">
    <label for="medication">Medicamento *</label>
    <input type="text" name="medication" id="medication" class="form-control @error('medication') is-invalid @enderror" value="{{ old('medication', $prescription->medication ?? '') }}" required>
    @error('medication')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="dosage">Dosagem *</label>
            <input type="text" name="dosage" id="dosage" class="form-control @error('dosage') is-invalid @enderror" value="{{ old('dosage', $prescription->dosage ?? '') }}" placeholder="Ex: 10" required>
            @error('dosage')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="unit">Unidade</label>
            <select name="unit" id="unit" class="form-control">
                <option value="">Selecione</option>
                @foreach(['mg/kg', 'mg', 'ml', 'mcg/kg', 'UI/kg', 'g'] as $u)
                    <option value="{{ $u }}" {{ old('unit', $prescription->unit ?? '') == $u ? 'selected' : '' }}>{{ $u }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="frequency">Frequência *</label>
            <input type="text" name="frequency" id="frequency" class="form-control @error('frequency') is-invalid @enderror" value="{{ old('frequency', $prescription->frequency ?? '') }}" placeholder="Ex: 2x ao dia" required>
            @error('frequency')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="route">Via</label>
            <select name="route" id="route" class="form-control">
                <option value="">Selecione</option>
                @foreach(['IV', 'IM', 'SC', 'VO', 'PO', 'SL', 'Tópico', 'Oftálmico', 'Intranasal', 'Retal'] as $r)
                    <option value="{{ $r }}" {{ old('route', $prescription->route ?? '') == $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="start_date">Data Início</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $prescription->start_date ?? '') }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="end_date">Data Fim</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $prescription->end_date ?? '') }}">
        </div>
    </div>
</div>
<div class="form-group">
    <label for="notes">Observações</label>
    <textarea name="notes" id="notes" rows="2" class="wysiwyg form-control">{{ old('notes', $prescription->notes ?? '') }}</textarea>
</div>
