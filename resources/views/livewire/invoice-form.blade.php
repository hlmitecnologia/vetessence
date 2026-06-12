<div>
    <form wire:submit.prevent="save">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Dados da Fatura</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tutor *</label>
                            <x-tom-select wire="tutor_id" :value="$tutor_id" required>
                                @foreach($tutors as $tutor)
                                <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                                @endforeach
                            </x-tom-select>
                            @error('tutor_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vencimento *</label>
                            <input type="date" wire:model="due_date" required class="form-control">
                            @error('due_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Itens</h5>
                <div class="card-tools">
                    <button type="button" wire:click="addItem" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus mr-1"></i> Adicionar Item
                    </button>
                </div>
            </div>
            <div class="card-body">

                @error('items') <span class="text-danger small d-block mb-2">{{ $message }}</span> @enderror

                @foreach($items as $index => $item)
                <div class="row align-items-end mb-2 p-3 bg-light rounded">
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Descrição</label>
                            <input type="text" wire:model="items.{{ $index }}.description" required class="form-control form-control-sm" placeholder="Descrição do item">
                            @error("items.{$index}.description") <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Qtd</label>
                            <input type="number" wire:model="items.{{ $index }}.quantity" min="1" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Valor Unit.</label>
                            <input type="number" wire:model="items.{{ $index }}.unit_price" step="0.01" min="0" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Total</label>
                            <p class="font-weight-bold mb-0 small">R$ {{ number_format($item['quantity'] * $item['unit_price'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-1 text-center">
                        @if(count($items) > 1)
                        <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        @endif
                    </div>
                </div>
                @endforeach

                <div class="d-flex justify-content-end mt-3">
                    <div style="width: 240px;">
                        <div class="d-flex justify-content-between py-1 border-top">
                            <span class="text-muted">Subtotal:</span>
                            <span class="font-weight-bold">R$ {{ number_format($this->subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-top">
                            <span class="font-weight-bold h5 mb-0">Total:</span>
                            <span class="font-weight-bold h5 mb-0 text-primary">R$ {{ number_format($this->subtotal, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right">
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Criar Fatura</button>
        </div>
    </form>
</div>
