@extends('home.layout')

@section('home-title') MYO Slots @endsection

@section('home-content')
{!! breadcrumbs(['Characters' => 'characters', 'MYO Slots' => 'myos']) !!}

<h1>
    MYO Slots
</h1>

<p>This is a list of MYO slots you own. Click the name of a slot to view details about it. MYO slots are non-transferrable and can be submitted for approval from their respective pages.</p>

<table class="table table-sm">
    <thead>
        <tr>
            <th>Slot Name</th>
            <th>Rarity</th>
            <th>Species</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach($slots as $slot)
            <tr>
                <td><a href="{{ $slot->viewUrl }}">{{ $slot->name }}</a></td>
                <td>{!! $slot->rarityId ? $slot->rarity->displayName : 'None' !!}</td>
                <td>{!! $slot->speciesId ? $slot->species->displayName : 'None' !!}</td>
                <td>{{ format_date($slot->created_at) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection