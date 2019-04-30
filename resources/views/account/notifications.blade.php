@extends('layouts.app')

@section('title') Notifications @endsection

@section('content')
{!! breadcrumbs(['My Account' => Auth::user()->url, 'Notifications' => 'notifications']) !!}

<h1>Notifications</h1>

<div class="text-right mb-3">
    {!! Form::open(['url' => 'notifications/clear']) !!}
        {!! Form::submit('Clear All', ['class' => 'btn btn-primary']) !!}
    {!! Form::close() !!}
</div>
{!! $notifications->render() !!}

<table class="table notifications-table">
    <thead>
        <tr>
            <th>Message</th>
            <th>Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($notifications as $notification)
            <tr class="{{ $notification->is_unread ? 'unread' : '' }}">
                <td>{!! $notification->message !!}</td>
                <td>{{ format_date($notification->created_at) }}</td>
                <td class="text-right"><a href="#" data-id="{{ $notification->id }}" class="clear-notification">×</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
@if(!count($notifications))
    <div class="text-center">No notifications.</div>
@endif

{!! $notifications->render() !!}

@endsection
@section('scripts')
@parent
<script>
    $( document ).ready(function(){

        $('.clear-notification').on('click', function(e) {
            e.preventDefault();
            var $row = $(this).parent().parent();
            $.get("{{ url('notifications/delete') }}/" + $(this).data('id'), function( data ) {
                console.log($(this));
                $row.fadeOut(300, function() { $(this).remove(); });
            });
        });

    });


</script>
@endsection