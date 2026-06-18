@extends('portal.layouts.app', ['title' => 'Agendar Consulta'])

@section('content')
<div class="mb-6">
    <a href="{{ route('portal.appointments.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left mr-1"></i>Consultas
    </a>
</div>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Agendar Consulta</h1>

    <form id="bookingForm" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pet</label>
            <select name="pet_id" id="pet_id" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm">
                <option value="">Selecione um pet</option>
                @foreach($pets as $pet)
                <option value="{{ $pet->id }}">{{ $pet->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo da consulta</label>
            <textarea name="reason" id="reason" rows="3" required
                class="wysiwyg w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm @error('reason') is-invalid @enderror"></textarea>
            @error('reason')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>

        <div class="border-t border-gray-200 pt-4 mt-4">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Escolha o veterinário e horário</h2>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                    <input type="date" name="date" id="date"
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Veterinário</label>
                    <select name="vet_id" id="vet_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm">
                        <option value="">Selecione um veterinário</option>
                    </select>
                </div>
            </div>

            <div id="slotsContainer" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Horários disponíveis</label>
                <div id="slotsGrid" class="grid grid-cols-4 gap-2"></div>
            </div>

            <div id="noSlotsMessage" class="hidden text-sm text-red-600 bg-red-50 p-3 rounded-lg">
                Nenhum horário disponível para esta data e veterinário.
            </div>
        </div>

        <button type="submit" id="submitBtn" disabled
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm disabled:opacity-50 disabled:cursor-not-allowed">
            Agendar consulta
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const dateInput = document.getElementById('date');
const vetSelect = document.getElementById('vet_id');
const slotsContainer = document.getElementById('slotsContainer');
const slotsGrid = document.getElementById('slotsGrid');
const noSlotsMessage = document.getElementById('noSlotsMessage');
const submitBtn = document.getElementById('submitBtn');
let selectedTime = null;

async function loadAvailableVets(date) {
    vetSelect.innerHTML = '<option value="">Carregando...</option>';
    slotsContainer.classList.add('hidden');
    noSlotsMessage.classList.add('hidden');
    submitBtn.disabled = true;
    selectedTime = null;

    try {
        const res = await fetch(`{{ route("portal.vet-availability.available-vets") }}?date=${date}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        vetSelect.innerHTML = '<option value="">Selecione um veterinário</option>';
        data.vets.forEach(vet => {
            vetSelect.innerHTML += `<option value="${vet.id}">${vet.name}${vet.crmv ? ' - CRMV ' + vet.crmv : ''}</option>`;
        });
    } catch {
        vetSelect.innerHTML = '<option value="">Erro ao carregar</option>';
    }
}

async function loadSlots(vetId, date) {
    slotsContainer.classList.add('hidden');
    noSlotsMessage.classList.add('hidden');
    slotsGrid.innerHTML = '';
    submitBtn.disabled = true;
    selectedTime = null;

    try {
        const res = await fetch(`{{ route("portal.vet-availability.vet-slots") }}?vet_id=${vetId}&date=${date}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.slots.length === 0) {
            noSlotsMessage.classList.remove('hidden');
            return;
        }

        data.slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500 transition text-center';
            btn.textContent = slot.label;
            btn.dataset.time = slot.time;
            btn.onclick = () => selectSlot(btn, slot.time);
            slotsGrid.appendChild(btn);
        });

        slotsContainer.classList.remove('hidden');
    } catch {
        noSlotsMessage.classList.remove('hidden');
        noSlotsMessage.textContent = 'Erro ao carregar horários.';
    }
}

function selectSlot(btn, time) {
    document.querySelectorAll('#slotsGrid button').forEach(b => {
        b.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
        b.classList.add('border-gray-300');
    });
    btn.classList.remove('border-gray-300');
    btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
    selectedTime = time;
    submitBtn.disabled = false;
}

dateInput.addEventListener('change', function() {
    if (this.value) {
        loadAvailableVets(this.value);
    }
});

vetSelect.addEventListener('change', function() {
    if (this.value && dateInput.value) {
        loadSlots(this.value, dateInput.value);
    }
});

document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!selectedTime) return;

    submitBtn.disabled = true;
    submitBtn.textContent = 'Agendando...';

    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('pet_id', document.getElementById('pet_id').value);
    formData.append('vet_id', vetSelect.value);
    formData.append('date', dateInput.value);
    formData.append('time', selectedTime);
    formData.append('reason', document.getElementById('reason').value);

    try {
        const response = await fetch('{{ route("portal.appointments.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (response.ok) {
            window.location.href = '{{ route("portal.appointments.index") }}?success=1';
        } else {
            const err = await response.json();
            alert(err.message || 'Erro ao agendar. Tente novamente.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Agendar consulta';
        }
    } catch {
        alert('Erro de conexão. Tente novamente.');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Agendar consulta';
    }
});
</script>
@endpush
