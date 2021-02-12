@extends('admin.layout')

@section('admin-title') Levels @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Levels' => 'admin/levels', ($level->id ? 'Edit' : 'Create').' Level' => $level->id ? 'admin/levels/edit/'.$level->id : 'admin/levels/create']) !!}

<h1>{{ $level->id ? 'Edit' : 'Create' }} Level
    @if($level->id)
        <a href="#" class="btn btn-outline-danger float-right delete-level-button">Delete Level</a>
    @endif
</h1>

{!! Form::open(['url' => $level->id ? 'admin/levels/edit/'.$level->id : 'admin/levels/create']) !!}

<h3>Basic Information</h3>
<p>All users start at level one</p>
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

<div class="form-group">
    {!! Form::label('Description') !!}
    {!! Form::text('description', $level->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<h3>Requirements</h3>
@include('widgets._level_limit_select', ['loots' => $level->limits])
<br>
<h3>Rewards</h3>
<p>Rewards are awarded when the user levels up</p>
@include('widgets._loot_select', ['loots' => $level->rewards, 'showLootTables' => true, 'showRaffles' => true])

<div class="text-right">
    {!! Form::submit($level->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@include('widgets._loot_select_row', ['items' => $items, 'currencies' => $currencies, 'tables' => $tables, 'raffles' => $raffles, 'showLootTables' => true, 'showRaffles' => true])
@include('widgets._level_limit_row', ['items' => $items, 'currencies' => $currencies])
@endsection

@section('scripts')
@parent
@include('js._loot_js', ['showLootTables' => true, 'showRaffles' => true])
@include('js._level_limit_js')
<script>
$( document ).ready(function() {    
    
    $('.delete-level-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/levels/delete') }}/{{ $level->id }}", 'Delete Level');
    });
});
    
</script>
@endsection