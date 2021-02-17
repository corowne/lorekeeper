@extends('admin.layout')

@section('admin-title') Levels @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Character Levels' => 'admin/levels/character']) !!}
<h1> Character Levels</h1>

<p>This is a list of levels in the game.</p>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/levels/character/create') }}"><i class="fas fa-plus"></i> Create New Character Level</a>
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

@if(!count($levels))
    <p>No levels found.</p>
@else
    {!! $levels->render() !!}

    <table class="table table-sm category-table">
      <thead>
          <tr>
              <th>Level</th>
              <th>EXP required</th>
              <th>Stat Point Award</th>
              <th></th>
          </tr>
      </thead>
      <tbody>
          @foreach($levels as $level)
              <tr class="sort-item" data-id="{{ $level->id }}">
                  <td>{{ $level->level }}</td>
                  <td>{{ $level->exp_required }}</td>
                  <td>{{ $level->stat_points }}</td>
                  <td class="text-right">
                      <a href="{{ url('admin/levels/character/edit/'.$level->id) }}" class="btn btn-primary">Edit</a>
                  </td>
              </tr>
          @endforeach
      </tbody>
  </table>
    {!! $levels->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection
