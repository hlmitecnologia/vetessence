import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import momentPlugin from '@fullcalendar/moment';

const CalendarApp = function () {
    let calendar = null;
    let currentAppointment = null;

    function init() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, momentPlugin],
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'pt-br',
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia',
                list: 'Lista'
            },
            firstDay: 1,
            height: 'auto',
            editable: true,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: 3,
            moreLinkText: (n) => `+${n} mais`,
            noEventsText: 'Nenhum agendamento',
            eventSources: [
                {
                    url: '/api/v1/appointments/calendar/data',
                    method: 'GET',
                    extraParams: function () {
                        return {
                            start: calendar.view.currentStart.toISOString(),
                            end: calendar.view.currentEnd.toISOString()
                        };
                    },
                    failure: function () {
                        console.error('Error fetching appointments');
                    }
                }
            ],
            eventClick: function (info) {
                currentAppointment = info.event;
                showAppointmentDetail(info.event);
            },
            eventDrop: function (info) {
                updateAppointmentDate(info.event);
            },
            eventResize: function (info) {
                updateAppointmentDate(info.event);
            },
            select: function (info) {
                window.location.href = '/appointments/create?date=' + info.startStr.slice(0, 10) + '&time=' + info.startStr.slice(11, 16);
            }
        });

        calendar.render();
    }

    function showAppointmentDetail(event) {
        const props = event.extendedProps;
        const statusLabels = {
            'scheduled': 'Agendado',
            'confirmed': 'Confirmado',
            'in_progress': 'Em Andamento',
            'completed': 'Concluído',
            'cancelled': 'Cancelado',
            'no_show': 'Faltou'
        };
        const statusColors = {
            'scheduled': 'primary',
            'confirmed': 'success',
            'in_progress': 'warning',
            'completed': 'secondary',
            'cancelled': 'danger',
            'no_show': 'dark'
        };

        const modal = document.getElementById('appointmentModal');
        if (!modal) return;

        document.getElementById('appointment-pet').textContent = props.pet_name || '-';
        document.getElementById('appointment-tutor').textContent = props.tutor_name || '-';
        document.getElementById('appointment-vet').textContent = props.vet_name || '-';
        document.getElementById('appointment-type').textContent = props.type || '-';
        document.getElementById('appointment-time').textContent = props.time || '-';
        document.getElementById('appointment-reason').textContent = props.reason || '-';
        document.getElementById('appointment-status').innerHTML =
            '<span class="badge badge-' + (statusColors[props.status] || 'secondary') + '">' +
            (statusLabels[props.status] || props.status) + '</span>';

        document.getElementById('appointment-view-btn').href = '/appointments/' + event.id;
        document.getElementById('appointment-edit-btn').href = '/appointments/' + event.id + '/edit';

        $('#appointmentModal').modal('show');
    }

    function updateAppointmentDate(event) {
        const start = event.start;
        const date = start.toISOString().slice(0, 10);
        const time = start.toISOString().slice(11, 19);

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/api/v1/appointments/' + event.id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                date: date,
                time: time
            })
        })
        .then(function (response) {
            if (!response.ok) {
                event.revert();
                throw new Error('Update failed');
            }
        })
        .catch(function (error) {
            console.error('Error updating appointment:', error);
            event.revert();
        });
    }

    return { init: init };
};

document.addEventListener('DOMContentLoaded', function () {
    var app = CalendarApp();
    app.init();
});
