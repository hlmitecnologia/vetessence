@extends('layouts.adminlte', ['title' => 'Novo Serviço'])

@section('header')
    <a href="{{ route('services.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Novo Serviço</h2>
@endsection

@section('content')
<form action="{{ route('services.store') }}" method="POST" class="max-w-2xl mx-auto">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <select name="category_id" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Selecione...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Preço *</label><input type="number" name="price" value="{{ old('price', 0) }}" step="0.01" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Duração (minutos)</label><input type="number" name="duration" value="{{ old('duration') }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label><textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ old('description') }}</textarea></div>
        </div>

        <hr class="my-6">
        <h5 class="font-semibold mb-3"><i class="fas fa-tags"></i> Preços por Espécie/Porte</h5>
        <div id="tiers" class="space-y-2">
            @if(old('tiers'))
                @foreach(old('tiers') as $i => $tier)
                <div class="flex gap-2 items-end tier-row">
                    <div><label class="text-xs">Espécie</label><select name="tiers[{{ $i }}][species]" class="form-control form-control-sm">
                        @foreach($speciesList as $s)
                        <option value="{{ $s }}" {{ ($tier['species'] ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select></div>
                    <div><label class="text-xs">Porte</label><input type="text" name="tiers[{{ $i }}][size]" value="{{ $tier['size'] ?? '' }}" class="form-control form-control-sm" placeholder="ex: Pequeno"></div>
                    <div><label class="text-xs">Preço</label><input type="number" name="tiers[{{ $i }}][price]" value="{{ $tier['price'] }}" step="0.01" class="form-control form-control-sm"></div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-tier"><i class="fas fa-times"></i></button>
                </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="add-tier"><i class="fas fa-plus"></i> Adicionar faixa</button>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('services.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let tierIndex = {{ old('tiers') ? count(old('tiers')) : 0 }};
document.getElementById('add-tier')?.addEventListener('click', function() {
    const html = `<div class="flex gap-2 items-end tier-row">
        <div><label class="text-xs">Espécie</label>
            <select name="tiers[${tierIndex}][species]" class="form-control form-control-sm">
                @foreach($speciesList as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
            </select></div>
        <div><label class="text-xs">Porte</label><input type="text" name="tiers[${tierIndex}][size]" class="form-control form-control-sm"></div>
        <div><label class="text-xs">Preço</label><input type="number" name="tiers[${tierIndex}][price]" step="0.01" class="form-control form-control-sm"></div>
        <button type="button" class="btn btn-sm btn-outline-danger remove-tier"><i class="fas fa-times"></i></button>
    </div>`;
    document.getElementById('tiers').insertAdjacentHTML('beforeend', html);
    tierIndex++;
});
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-tier')) {
        e.target.closest('.tier-row').remove();
    }
});
</script>
@endpush
