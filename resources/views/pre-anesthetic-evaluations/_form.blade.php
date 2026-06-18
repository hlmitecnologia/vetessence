<div class="form-group"><label>Pet</label><x-tom-select name="pet_id" :value="old('pet_id', $preAnestheticEvaluation->pet_id ?? '')" required>
    @foreach($pets as $pet)<option value="{{ $pet->id }}" {{ old('pet_id', $preAnestheticEvaluation->pet_id ?? '') == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ optional($pet->tutors->first())->name }}</option>@endforeach
</x-tom-select></div>
<div class="form-group"><label>ASA Score</label>
    <select name="asa_score" class="form-control" required>
        @for($i=1;$i<=6;$i++)<option value="{{ $i }}" {{ old('asa_score', $preAnestheticEvaluation->asa_score ?? '') == $i ? 'selected' : '' }}>ASA {{ $i }}</option>@endfor
    </select></div>
<div class="form-check"><input type="checkbox" name="fasted" class="form-check-input" value="1" {{ old('fasted', $preAnestheticEvaluation->fasted ?? false) ? 'checked' : '' }}><label class="form-check-label">Jejum</label></div>
<div class="form-check"><input type="checkbox" name="hydrated" class="form-check-input" value="1" {{ old('hydrated', $preAnestheticEvaluation->hydrated ?? false) ? 'checked' : '' }}><label class="form-check-label">Hidratado</label></div>
<div class="form-group"><label>Status</label>
    <select name="status" class="form-control" required>
        <option value="pending" {{ old('status', $preAnestheticEvaluation->status ?? '') == 'pending' ? 'selected' : '' }}>Pendente</option>
        <option value="approved" {{ old('status', $preAnestheticEvaluation->status ?? '') == 'approved' ? 'selected' : '' }}>Aprovado</option>
        <option value="rejected" {{ old('status', $preAnestheticEvaluation->status ?? '') == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
    </select></div>
<div class="wysiwyg form-group"><label>Observações</label><textarea name="observations" class="wysiwyg form-control @error('observations') is-invalid @enderror">{{ old('observations', $preAnestheticEvaluation->observations ?? '') }}</textarea>@error('observations')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
<div class="wysiwyg form-group"><label>Recomendações</label><textarea name="recommendations" class="wysiwyg form-control @error('recommendations') is-invalid @enderror">{{ old('recommendations', $preAnestheticEvaluation->recommendations ?? '') }}</textarea>@error('recommendations')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
