@extends('layouts.app')

@section('title')
    Raffles
@endsection

@section('content')
    {!! breadcrumbs(['Raffles' => 'raffles']) !!}
    <h1>Raffles</h1>
    <p>Click on the name of a raffle to view the tickets, and in the case of completed raffles, the winners. Raffles in a group with a title will be rolled consecutively starting from the top, and will not draw duplicate winners.</p>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a href="{{ url()->current() }}" class="nav-link {{ Request::get('view') ? '' : 'active' }}">Current Raffles</a></li>
        <li class="nav-item"><a href="{{ url()->current() }}?view=completed" class="nav-link {{ Request::get('view') == 'completed' ? 'active' : '' }}">Completed Raffles</a></li>
    </ul>

    @if (count($raffles))
        <?php $prevGroup = null; ?>
        <ul class="list-group mb-3">
            @foreach ($raffles as $raffle)
                @if ($prevGroup != $raffle->group_id)
        </ul>
        @if ($prevGroup)
            </div>
        @endif
        <div class="card mb-3">
            <div class="card-header h3">{{ $groups[$raffle->group_id]->name }}</div>
            <ul class="list-group list-group-flush">
    @endif

    <li class="list-group-item">
        <x-admin-edit title="Raffle" :object="$raffle" />
        <a href="{{ url('raffles/view/' . $raffle->id) }}">{{ $raffle->name }}</a>
    </li>
    <?php $prevGroup = $raffle->group_id; ?>
    @endforeach
@else
    <p>No raffles found.</p>
    @endif
@endsection
@section('scripts')
    @parent
@endsection
