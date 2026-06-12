<div class="form-group">
    <label for="fluid_type">Tipo de Fluido *</label>
    <select name="fluid_type" id="fluid_type" class="form-control @error('fluid_type') is-invalid @enderror" required>
        <option value="">Selecione</option>
        @foreach(['Ringer Lactato', 'Ringer Simples', 'SF 0,9%', 'Glicose 5%', 'Glicose 50%', 'NaCl 7,5%', 'Hetastarch', 'Plasma', 'Sangue Total'] as $fluid)
            <option value="{{ $fluid }}" {{ old('fluid_type', $fluidTherapy->fluid_type ?? '') == $fluid ? 'selected' : '' }}>{{ $fluid }}</option>
        @endforeach
    </select>
    @error('fluid_type')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="rate">Taxa de Infusão (ml/kg/h)</label>
            <input type="number" step="0.1" name="rate" id="rate" class="form-control" value="{{ old('rate', $fluidTherapy->rate ?? '') }}" placeholder="Ex: 5">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="volume">Volume Total (ml)</label>
            <input type="number" step="0.1" name="volume" id="volume" class="form-control" value="{{ old('volume', $fluidTherapy->volume ?? '') }}" placeholder="Ex: 500">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="route">Via de Administração</label>
            <select name="route" id="route" class="form-control">
                <option value="">Selecione</option>
                @foreach(['IV', 'SC', 'IO', 'VO'] as $r)
                    <option value="{{ $r }}" {{ old('route', $fluidTherapy->route ?? '') == $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="start_time">Início</label>
            <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', isset($fluidTherapy->start_time) ? $fluidTherapy->start_time->format('Y-m-d\TH:i') : '') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="end_time">Término</label>
            <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', isset($fluidTherapy->end_time) ? $fluidTherapy->end_time->format('Y-m-d\TH:i') : '') }}">
        </div>
    </div>
</div>
<div class="form-group">
    <label for="observations">Observações</label>
    <textarea name="observations" id="observations" rows="2" class="wysiwyg form-control">{{ old('observations', $fluidTherapy->observations ?? '') }}</textarea>
</div>
