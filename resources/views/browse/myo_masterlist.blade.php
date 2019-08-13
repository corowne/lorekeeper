@extends('layouts.app')

@section('title') MYO Slot Masterlist @endsection

@section('content')
{!! breadcrumbs(['MYO Slot Masterlist' => 'myos']) !!}
<h1>MYO Slot Masterlist</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('rarity_id', $rarities, Request::get('rarity_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

{!! $slots->render() !!}
<table class="table table-sm">
    <thead>
        <tr>
            <th>Owner</th>
            <th>Slot Name</th>
            <th>Rarity</th>
            <th>Species</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach($slots as $slot)
            <tr>
                <td>{!! $slot->displayOwner !!}</td>
                <td><a href="{{ $slot->viewUrl }}">{{ $slot->name }}</a></td>
                <td>{!! $slot->rarityId ? $slot->rarity->displayName : 'None' !!}</td>
                <td>{!! $slot->speciesId ? $slot->species->displayName : 'None' !!}</td>
                <td>{{ format_date($slot->created_at) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $slots->render() !!}

<div class="text-center mt-4 small text-muted">{{ $slots->total() }} result{{ $slots->total() == 1 ? '' : 's' }} found.</div>

@endsection
