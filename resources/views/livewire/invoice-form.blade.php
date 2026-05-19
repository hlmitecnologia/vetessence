<div>
    <form wire:submit.prevent="save">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold mb-4">Dados da Fatura</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tutor *</label>
                    <x-tom-select wire="tutor_id" :value="$tutor_id" required>
                        @foreach($tutors as $tutor)
                        <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                        @endforeach
                    </x-tom-select>
                    @error('tutor_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vencimento *</label>
                    <input type="date" wire:model="due_date" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @error('due_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Itens</h3>
                <button type="button" wire:click="addItem" class="text-indigo-600 hover:text-indigo-800 text-sm">
                    <i class="fas fa-plus mr-1"></i> Adicionar Item
                </button>
            </div>

            @error('items') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div class="space-y-3">
                @foreach($items as $index => $item)
                <div class="flex gap-4 items-end p-4 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500 mb-1">Descrição</label>
                        <input type="text" wire:model="items.{{ $index }}.description" required
                               class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Descrição do item">
                        @error("items.{$index}.description") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="w-20">
                        <label class="block text-xs text-gray-500 mb-1">Qtd</label>
                        <input type="number" wire:model="items.{{ $index }}.quantity" min="1"
                               class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div class="w-28">
                        <label class="block text-xs text-gray-500 mb-1">Valor Unit.</label>
                        <input type="number" wire:model="items.{{ $index }}.unit_price" step="0.01" min="0"
                               class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div class="w-24 text-right">
                        <label class="block text-xs text-gray-500 mb-1">Total</label>
                        <span class="font-semibold">R$ {{ number_format($item['quantity'] * $item['unit_price'], 2, ',', '.') }}</span>
                    </div>
                    @if(count($items) > 1)
                    <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700 mb-1">
                        <i class="fas fa-trash"></i>
                    </button>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <div class="w-64">
                    <div class="flex justify-between py-2 border-t">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-t pt-2">
                        <span class="font-bold text-lg">Total:</span>
                        <span class="font-bold text-lg text-indigo-600">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('invoices.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Criar Fatura
            </button>
        </div>
    </form>
</div>
