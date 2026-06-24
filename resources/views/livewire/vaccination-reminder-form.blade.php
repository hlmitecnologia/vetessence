<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Pet *</label>
            <x-tom-select wire="pet_id" :value="$pet_id" required>
                @foreach($pets as $pet)
                    <option value="{{ $pet->id }}">{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                @endforeach
            </x-tom-select>
            @error('pet_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Vacina (Registro) *</label>
            <x-tom-select wire="vaccination_id" :value="$vaccination_id" required>
                @foreach($vaccinations as $vac)
                    <option value="{{ $vac->id }}">{{ $vac->vaccine }}</option>
                @endforeach
            </x-tom-select>
            @error('vaccination_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data Agendada *</label>
                    <input type="date" wire:model="scheduled_date" class="form-control @error('scheduled_date') is-invalid @enderror" required>
                    @error('scheduled_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status *</label>
                    <select wire:model="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="pending">Pendente</option>
                        <option value="sent">Enviado</option>
                        <option value="failed">Falhou</option>
                    </select>
                    @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Canal</label>
            <select wire:model="channel" class="form-control">
                <option value="">Selecione...</option>
                <option value="sms">SMS</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="email">E-mail</option>
            </select>
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model="notes" class="wysiwyg form-control @error('notes') is-invalid @enderror" rows="2" maxlength="500"></textarea>
            @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
