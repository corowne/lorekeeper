@extends('home.layout')

@section('home-title') Bug Reports @endsection

@section('home-content')
    {!! breadcrumbs(['Reports' => 'reports']) !!}
<h1>
Bug Reports
</h1>

<p>Please check the current 'fix in progress' reports to ensure your bug is not already being worked on! If the title is not descriptive enough, or does not match your bug, feel free to create a new one.</p>
<div class="alert alert-warning">Please note that certain bug reports cannot be viewed until they are closed to prevent abuse.</div>

@if(Auth::check())
    <div class="text-right">
            <a href="{{ url('reports/new') }}" class="btn btn-success">New Report</a>
    </div>
@endif
<br>
{!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('url', Request::get('url'), ['class' => 'form-control', 'placeholder' => 'URL / Title']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}

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
