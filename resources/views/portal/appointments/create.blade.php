@extends('portal.layouts.app', ['title' => 'Agendar Consulta'])

@section('content')
@php
    $preselectedPetId = request()->query('pet_id');
    $preselectedReason = request()->query('reason');
@endphp
<div class="mb-6">
    <a href="{{ route('portal.appointments.index') }}" class="text-base text-blue-600 hover:text-blue-700 touch-target-sm inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i>Consultas
    </a>
</div>

<div class="max-w-2xl mx-auto portal-card p-8 sm:p-10 portal-fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 flex items-center gap-3">
        <i class="fas fa-calendar-plus" style="color: var(--brand-primary, #455e36)"></i>
        Agendar Consulta
    </h1>

    <form id="bookingForm" class="space-y-6">
        @csrf

        <div>
            <label class="portal-label">Clínica *</label>
            <select name="branch_id" id="branch_id" required class="portal-input">
                <option value="">Selecione uma clínica</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="portal-label">Pet *</label>
            <select name="pet_id" id="pet_id" required class="portal-input">
                <option value="">Selecione um pet</option>
                @foreach($pets as $pet)
                <option value="{{ $pet->id }}" {{ $preselectedPetId == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="portal-label">Motivo da consulta</label>
            <textarea name="reason" id="reason" rows="3"
                class="wysiwyg portal-input @error('reason') is-invalid @enderror">{{ $preselectedReason }}</textarea>
            @error('reason')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>

        <div class="border-t border-gray-200 pt-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Escolha o veterinário e horário</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="portal-label">Data</label>
                    <input type="date" name="date" id="date"
                        min="{{ date('Y-m-d') }}"
                        class="portal-input">
                </div>
                <div>
                    <label class="portal-label">Veterinário</label>
                    <select name="vet_id" id="vet_id" class="portal-input">
                        <option value="">Selecione um veterinário</option>
                    </select>
                </div>
            </div>

            <div id="slotsContainer" class="hidden mt-4">
                <label class="portal-label mb-3">Horários disponíveis</label>
                <div id="slotsGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-3"></div>
            </div>

            <div id="noSlotsMessage" class="hidden text-base text-red-600 bg-red-50 p-4 rounded-xl">
                Nenhum horário disponível para esta data e veterinário.
            </div>
        </div>

        <button type="submit" id="submitBtn" disabled
            class="portal-btn w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-lg disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="fas fa-check-circle"></i>
            Agendar consulta
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const branchSelect = document.getElementById('branch_id');
const dateInput = document.getElementById('date');
const vetSelect = document.getElementById('vet_id');
const slotsContainer = document.getElementById('slotsContainer');
const slotsGrid = document.getElementById('slotsGrid');
const noSlotsMessage = document.getElementById('noSlotsMessage');
const submitBtn = document.getElementById('submitBtn');
let selectedTime = null;

async function loadAvailableVets(date, branchId) {
    vetSelect.innerHTML = '<option value="">Carregando...</option>';
    slotsContainer.classList.add('hidden');
    noSlotsMessage.classList.add('hidden');
    submitBtn.disabled = true;
    selectedTime = null;

    try {
        const params = new URLSearchParams({ date, branch_id: branchId });
        const res = await fetch(`{{ route("portal.vet-availability.available-vets") }}?${params}`, {
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
            btn.className = 'px-4 py-3 text-base border border-gray-300 rounded-xl hover:bg-blue-50 hover:border-blue-500 transition text-center touch-target';
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

function canLoadVets() {
    return branchSelect.value && dateInput.value;
}

branchSelect.addEventListener('change', function() {
    if (dateInput.value) {
        loadAvailableVets(dateInput.value, this.value);
    }
});

dateInput.addEventListener('change', function() {
    if (canLoadVets()) {
        loadAvailableVets(this.value, branchSelect.value);
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
    formData.append('branch_id', branchSelect.value);
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
