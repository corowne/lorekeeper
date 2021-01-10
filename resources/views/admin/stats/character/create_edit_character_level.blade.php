@extends('admin.layout')

@section('admin-title') Levels @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Levels' => 'admin/levels/character', ($level->id ? 'Edit' : 'Create').' Level' => $level->id ? 'admin/levels/character/edit/'.$level->id : 'admin/levels/character/create']) !!}

<h1>{{ $level->id ? 'Edit' : 'Create' }} Level
    @if($level->id)
        <a href="#" class="btn btn-outline-danger float-right delete-level-button">Delete Level</a>
    @endif
</h1>

{!! Form::open(['url' => $level->id ? 'admin/levels/character/edit/'.$level->id : 'admin/levels/character/create']) !!}

<h3>Basic Information</h3>
<p>All characters start at level one</p>
<div class="row">
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Level (e.g 2)') !!} 
            {!! Form::number('level', $level->level, ['class' => 'form-control', 'min' => 2]) !!}
        </div>
    </div>
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('EXP Required') !!}
            {!! Form::number('exp_required', $level->exp_required, ['class' => 'form-control', 'min' => 1]) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Stat Points Award (optional, input 0 for no reward)') !!} {!! add_help('Points awarded to User for levelling up.') !!}
    {!! Form::number('stat_points', $level->stat_points, ['class' => 'form-control', 'min' => 0]) !!}
</div>

<div class="text-right">
    {!! Form::submit($level->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary', 'min' => 0]) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    
    $('.delete-level-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/levels/character/delete') }}/{{ $level->id }}", 'Delete Level');
    });
});
    
</script>
@endsection