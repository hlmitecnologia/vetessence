@extends('layouts.adminlte', ['title' => 'Agenda'])

@push('styles')
<style>
    #calendar { max-width: 100%; margin: 0 auto; }
    .fc-event { cursor: pointer; }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <div class="btn-group">
            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Consulta
            </a>
        </div>
    </div>
    <div class="col-md-4 text-right">
        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-table"></i> Modo Tabela
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- Appointment Detail Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Agendamento</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr><th>Pet</th><td id="appointment-pet"></td></tr>
                    <tr><th>Tutor</th><td id="appointment-tutor"></td></tr>
                    <tr><th>Veterinário</th><td id="appointment-vet"></td></tr>
                    <tr><th>Tipo</th><td id="appointment-type"></td></tr>
                    <tr><th>Horário</th><td id="appointment-time"></td></tr>
                    <tr><th>Status</th><td id="appointment-status"></td></tr>
                    <tr><th>Motivo</th><td id="appointment-reason"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <a id="appointment-view-btn" href="#" class="btn btn-info">
                    <i class="fas fa-eye"></i> Visualizar
                </a>
                <a id="appointment-edit-btn" href="#" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.20/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.20/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.20/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.20/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin, FullCalendar.interactionPlugin],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'pt-br',
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            day: 'Dia'
        },
        firstDay: 1,
        height: 'auto',
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: 3,
        noEventsText: 'Nenhum agendamento',
        events: {
            url: '/api/v1/appointments/calendar/data',
            method: 'GET',
            extraParams: function () {
                return {
                    start: calendar.view.currentStart.toISOString(),
                    end: calendar.view.currentEnd.toISOString()
                };
            },
            failure: function () {
                console.error('Erro ao carregar agendamentos');
            }
        },
        eventClick: function (info) {
            var props = info.event.extendedProps;
            var statusLabels = {
                'scheduled': 'Agendado',
                'confirmed': 'Confirmado',
                'in_progress': 'Em Andamento',
                'completed': 'Concluído',
                'cancelled': 'Cancelado',
                'no_show': 'Faltou'
            };
            var statusColors = {
                'scheduled': 'primary',
                'confirmed': 'success',
                'in_progress': 'warning',
                'completed': 'secondary',
                'cancelled': 'danger',
                'no_show': 'dark'
            };

            document.getElementById('appointment-pet').textContent = props.pet_name || '-';
            document.getElementById('appointment-tutor').textContent = props.tutor_name || '-';
            document.getElementById('appointment-vet').textContent = props.vet_name || '-';
            document.getElementById('appointment-type').textContent = props.type || '-';
            document.getElementById('appointment-time').textContent = props.time || '-';
            document.getElementById('appointment-reason').textContent = props.reason || '-';
            document.getElementById('appointment-status').innerHTML =
                '<span class="badge badge-' + (statusColors[props.status] || 'secondary') + '">' +
                (statusLabels[props.status] || props.status) + '</span>';

            document.getElementById('appointment-view-btn').href = '/appointments/' + info.event.id;
            document.getElementById('appointment-edit-btn').href = '/appointments/' + info.event.id + '/edit';

            $('#appointmentModal').modal('show');
        },
        eventDrop: function (info) {
            updateAppointmentDate(info);
        },
        eventResize: function (info) {
            updateAppointmentDate(info);
        },
        select: function (info) {
            window.location.href = '/appointments/create?date=' + info.startStr.slice(0, 10) + '&time=' + info.startStr.slice(11, 16);
        }
    });

    calendar.render();

    function updateAppointmentDate(info) {
        var start = info.event.start;
        var date = start.toISOString().slice(0, 10);
        var time = start.toISOString().slice(11, 19);
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/appointments/' + info.event.id + '/reschedule', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ date: date, time: time })
        })
        .then(function (response) {
            if (!response.ok) {
                info.revert();
                throw new Error('Erro ao atualizar');
            }
        })
        .catch(function (error) {
            console.error('Erro ao atualizar agendamento:', error);
            info.revert();
        });
    }
});
</script>
@endpush
