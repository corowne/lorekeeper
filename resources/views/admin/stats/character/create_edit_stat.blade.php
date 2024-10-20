@extends('admin.layout')

@section('admin-title') Stats @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Stats' => 'admin/stats', ($stat->id ? 'Edit' : 'Create').' Stat' => $stat->id ? 'admin/stats/edit/'.$stat->id : 'admin/stats/create']) !!}

<h1>{{ $stat->id ? 'Edit' : 'Create' }} Stat
    @if($stat->id)
        <a href="#" class="btn btn-outline-danger float-right delete-stat-button">Delete Stat</a>
    @endif
</h1>

{!! Form::open(['url' => $stat->id ? 'admin/stats/edit/'.$stat->id : 'admin/stats/create']) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $stat->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Abbreviation') !!}
            {!! Form::text('abbreviation', $stat->abbreviation, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('default') !!} {!! add_help('This is the \'default\' or \'starter\' amount of stat.') !!}
    {!! Form::number('default', $stat->default, ['class' => 'form-control', 'min' => 1]) !!}
</div>

<p>Multiplier can apply to step (e.g (current + step) X Multiplier) or just to current. Leave step blank if you want it to apply just to current</p>
<p>If a stat calculation is a decimal it will round to the nearest whole number.</p>
<div class="row">
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Step (optional)') !!} {!! add_help('If you want a stat to increase more than by 1 per level up, enter a unique step here.') !!}
            {!! Form::text('step', $stat->step, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Multiplier (optional)') !!} {!! add_help('If you want the stat to increase based on a multiplication set it here.') !!}
            {!! Form::text('multiplier', $stat->multiplier, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<p>A max level can be applied if you want to cap the levels a character can gain</p>
<div class="form-group">
    {!! Form::label('Max level (optional)') !!} 
    {!! Form::text('max_level', $stat->max_level, ['class' => 'form-control']) !!}
</div>

<div class="text-right">
    {!! Form::submit($stat->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    
    $('.delete-stat-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/stats/delete') }}/{{ $stat->id }}", 'Delete Stat');
    });
});
    
</script>
@endsection