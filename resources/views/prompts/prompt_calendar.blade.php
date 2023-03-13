@extends('prompts.layout')

@section('title') Prompt Calendar @endsection

@section('content')
{!! breadcrumbs(['Prompts' => 'prompts', 'Prompt Calendar ' => 'prompts/prompt-calendar']) !!}
<h1>Prompt Calendar</h1>

<div id='calendar'></div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.js'></script>
    <script>

      document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar')
        const calendar = new FullCalendar.Calendar(calendarEl, {
			initialView: 'multiMonthYear',
			multiMonthMaxColumns: 2,
			lazyFetching: true,
			headerToolbar: {
				left: 'prev,next',
				center: 'title',
				right: 'multiMonthYear timeGridWeek timeGridDay',
			},
			eventSources: [
				{
					url: 'prompt-json',
					format: 'json',
				}
			]
        })
        calendar.render()
      })
    </script>

@endsection
