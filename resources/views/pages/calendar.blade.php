@extends('layouts.app')

@section('title')
    Calendar
@endsection

@section('sidebar')
    <ul>
        <li class="sidebar-header"><a href="{{ url('/calendar') }}" class="card-link">Calendar</a></li>
        <li class="sidebar-section">
            <div class="sidebar-section-header">Items</div>
            <div class="sidebar-item"><a href="{{ url('prompts') }}" class="{{ set_active('prompts') }}">Prompts</a></div>
            <div class="sidebar-item"><a href="{{ url('news') }}" class="{{ set_active('news') }}">News</a></div>
            <div class="sidebar-item"><a href="{{ url('sales') }}" class="{{ set_active('sales') }}">Sales</a></div>
        </li>
    </ul>
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Calendar' => 'world/calendar']) !!}
    <h1>Calendar</h1>

    <div id='calendar'></div>

    <script src="{{ asset('js/calendar.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar')
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                lazyFetching: true,
                height: '60vh',
                timeZone: "{{ config('app.timezone') }}",
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth multiMonthYear listWeek',
                },
                eventSources: [{
                    url: "{{ url('calendar/events') }}",
                    format: 'json',
                }]
            })
            calendar.render()
        })
    </script>
@endsection
