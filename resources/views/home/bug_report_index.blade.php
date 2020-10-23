@extends('home.layout')

@section('home-title') Bug Reports @endsection

@section('home-content')
    {!! breadcrumbs(['Reports' => 'reports']) !!}
<h1>
Bug Reports
</h1>

<p>Please check the current 'fix in progress' reports to ensure your bug is not already being worked on! If the title is not descriptive enough, or does not match your bug, feel free to create a new one.</p>
<div class="alert alert-warning">Please note that bug reports cannot be viewed unless they are closed to prevent users abusing exploits.</div>

<div class="text-right">
        <a href="{{ url('reports/new') }}" class="btn btn-success">To make a new report, please go here</a>
</div>
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
