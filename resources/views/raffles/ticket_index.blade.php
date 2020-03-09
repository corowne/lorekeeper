@extends('layouts.app')

@section('title') Raffle - {{ $raffle->name }} @endsection

@section('content')
{!! breadcrumbs(['Raffles' => 'raffles', 'Raffle: ' . $raffle->name => 'raffles/view/'.$raffle->id]) !!}
<h1>Raffle: {{ $raffle->name }}</h1>
@if($raffle->is_active == 1)
    <div class="alert alert-success">This raffle is currently open. (Number of winners to be drawn: {{ $raffle->winner_count }})</div>
@elseif($raffle->is_active == 2)
    <div class="alert alert-danger">This raffle is closed. Rolled: {!! format_date($raffle->rolled_at) !!}</div>
    <div class="card mb-3">
        <div class="card-header h3">Winner(s)</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><th class="col-xs-1 text-center" style="width: 100px;">#</th><th>User</th></thead>
                <tbody>
                    @foreach($raffle->tickets()->winners()->get() as $winner)
                        <tr>
                            <td class="text-center">{{ $winner->position }}</td>
                            <td class="text-left">{!! $winner->displayHolderName !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
<h3>Tickets</h3>

@if(Auth::check())
<p class="text-center">You {{ $raffle->is_active == 2 ? 'had' : 'have' }} <strong>{{ $userCount }}</strong> out of <strong>{{ $count }} tickets</strong> in this raffle.</p>
@endif

<div class="text-right">{!! $tickets->render() !!}</div>
<div class="table-responsive">
    <table class="table table-sm">
        <thead><th class="col-xs-1 text-center" style="width: 100px;">#</th><th>User</th></thead>
        <tbody>
            @foreach($tickets as $count=>$ticket)
                <tr {{ Auth::check() && $ticket->user_id && $ticket->user->name == Auth::user()->name ? 'class=inflow' : '' }}>
                    <td class="text-center">{{ $page * 100 + $count + 1 }}</td>
                    <td>{!! $ticket->displayHolderName !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="text-right">{!! $tickets->render() !!}</div>
@endsection
@section('scripts')
@parent
@endsection