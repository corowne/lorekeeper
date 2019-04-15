@extends('admin.layout')

@section('admin-title') Currencies @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Currencies' => 'admin/data/currencies']) !!}

<h1>Currencies</h1>

<p>This is a list of currencies that can be earned by users and/or characters. While they're collectively called "currencies", they can be used to track activity counts, event-only reward points, etc. and are not necessarily transferrable and/or can be spent. More information can be found on the creating/editing pages.</p>

<p>The order of currencies as displayed on user and character profiles can be edited from the <a href="{{ url('admin/data/currencies/sort') }}">Sort Currencies</a> page.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/currencies/create') }}"><i class="fas fa-plus"></i> Create New Currency</a></div>
{!! $currencies->render() !!}
<table class="table table-sm">
    <thead>
        <tr>
            <th>Name</th><th>Displays As</th><th>Description</th><th>Attaches To</th>
        </tr>
    </thead>
    <tbody>
        @foreach($currencies as $currency)
            <tr>
                <td><a href="{{ url('admin/data/currencies/edit/'.$currency->id) }}">{{ $currency->name }}</a> @if($currency->abbreviation) ({{ $currency->abbreviation }}) @endif</td>
                <td>{!! $currency->display(100) !!}</td>
                <td>{{ $currency->description }}</td>
                <td>
                    <div>{{ $currency->is_user_owned ? 'User' : '' }}</div>
                    <div>{{ $currency->is_character_owned ? 'Character' : '' }}</div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $currencies->render() !!}

@endsection