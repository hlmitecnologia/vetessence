<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Tipo *</label>
            <select wire:model="type" class="form-control @error('type') is-invalid @enderror" required>
                <option value="">Selecione</option>
                <option value="reminder">Lembrete</option>
                <option value="recall">Rechamada</option>
                <option value="promotional">Promocional</option>
                <option value="notification">Notificação</option>
                <option value="other">Outro</option>
            </select>
            @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Canal *</label>
            <select wire:model="channel" class="form-control @error('channel') is-invalid @enderror" required>
                <option value="">Selecione</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="email">E-mail</option>
                <option value="sms">SMS</option>
                <option value="push">Push</option>
            </select>
            @error('channel') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Assunto</label>
            <input type="text" wire:model="subject" class="form-control">
        </div>

        <div class="form-group">
            <label>Conteúdo *</label>
            <textarea wire:model="content" class="wysiwyg form-control @error('content') is-invalid @enderror" rows="5" required></textarea>
            @error('content') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="commTemplateIsActive">
                <label class="custom-control-label" for="commTemplateIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
