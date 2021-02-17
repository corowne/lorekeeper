@extends('admin.layout')

@section('admin-title') Stats @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Stats' => 'admin/stats']) !!}

<h1>Stats</h1>

<p>This is a list of stats in the game. Stats ONLY affect characters.</p>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/stats/create') }}"><i class="fas fa-plus"></i> Create New Stat</a>
</div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($stats))
    <p>No stats found.</p>
@else
    {!! $stats->render() !!}

    <table class="table table-sm category-table">
      <thead>
          <tr>
              <th>Name</th>
              <th>Abbreviation</th>
              <th>Default</th>
              <th>Step</th>
              <th>Multiplier</th>
              <th></th>
          </tr>
      </thead>
      <tbody>
          @foreach($stats as $stat)
              <tr class="sort-item" data-id="{{ $stat->id }}">
                  <td>{{ $stat->name }}</td>
                  <td>{{ $stat->abbreviation }}</td>
                  <td>{{ $stat->default }}</td>
                  <td>@if($stat->step) {{ $stat->step}} @else No Step @endif</td>
                  <td>@if($stat->multiplier) {{ $stat->multiplier}} @else No Multiplier @endif</td>
                  <td class="text-right">
                      <a href="{{ url('admin/stats/edit/'.$stat->id) }}" class="btn btn-primary">Edit</a>
                  </td>
              </tr>
          @endforeach
      </tbody>
  </table>
    {!! $stats->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection
