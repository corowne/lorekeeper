@extends('home.layout')

@section('home-title') Reports @endsection

@section('home-content')
    {!! breadcrumbs(['Reports' => 'reports']) !!}
<h1>
My Reports
</h1>

<div class="text-right">
        <a href="{{ url('reports/new') }}" class="btn btn-success">New Report</a>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !Request::get('type') || Request::get('type') == 'pending' ? 'active' : '' }}" href="{{ url('reports') }}">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'approved'  }}" href="{{ url('reports') . '?type=assigned' }}">Assigned</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'closed'  }}" href="{{ url('reports') . '?type=closed' }}">Closed</a>
    </li>
</ul>

@if(count($reports))
    {!! $reports->render() !!}
    <table class="table table-sm">
        <thead>
            <tr>
                <th width="30%">Link / Title</th>
                <th width="20%">Submitted</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                @include('home._report', ['report' => $report])
            @endforeach
        </tbody>
    </table>
    {!! $reports->render() !!}
@else 
    <p>No reports found.</p>
@endif

@endsection
