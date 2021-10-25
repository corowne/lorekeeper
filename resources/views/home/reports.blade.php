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
    <div class="row ml-md-2">
      <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
        <div class="col-6 col-md-4 font-weight-bold">Link/Title</div>
        <div class="col-6 col-md-5 font-weight-bold">Submitted</div>
        <div class="col-12 col-md-1 font-weight-bold">Status</div>
      </div>
            @foreach($reports as $report)
                @include('home._report', ['report' => $report])
            @endforeach
      </div>
    {!! $reports->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $reports->total() }} result{{ $reports->total() == 1 ? '' : 's' }} found.</div>
@else 
    <p>No reports found.</p>
@endif

@endsection
