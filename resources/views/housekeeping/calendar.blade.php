{{-- resources/views/housekeeping/calendar.blade.php --}}
@extends('layouts.app')

@section('title', 'Housekeeping Calendar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Housekeeping Calendar</h3>
                        <div>
                            <a href="{{ route('housekeeping.create') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i> New Task
                            </a>
                            <a href="{{ route('housekeeping.index') }}" class="btn btn-secondary">
                                <i class="fa fa-list"></i> List View
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
$(document).ready(function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: @json($tasks),
        eventClick: function(info) {
            // Redirect to task view when event is clicked
            window.location.href = "{{ url('housekeeping') }}/" + info.event.id;
        },
        eventDidMount: function(info) {
            // Add tooltip with additional information
            $(info.el).tooltip({
                title: `
                    <strong>Assigned to:</strong> ${info.event.extendedProps.assigned_to}<br>
                    <strong>Priority:</strong> ${info.event.extendedProps.priority}<br>
                    <strong>Status:</strong> ${info.event.extendedProps.status}
                `,
                html: true,
                placement: 'top'
            });
        }
    });
    calendar.render();
});
</script>
@endpush
