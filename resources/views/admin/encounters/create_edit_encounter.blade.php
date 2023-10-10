@extends('admin.layout')

@section('admin-title') Encounters @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Encounters' => 'admin/data/encounters', ($encounter->id ? 'Edit' : 'Create').' Encounter' => $encounter->id ? 'admin/data/encounters/edit/'.$encounter->id : 'admin/data/encounters/create']) !!}

<h1>{{ $encounter->id ? 'Edit' : 'Create' }} Encounter
    @if($encounter->id)
        <a href="#" class="btn btn-danger float-right delete-encounter-button">Delete Encounter</a>
    @endif
</h1>

{!! Form::open(['url' => $encounter->id ? 'admin/data/encounters/edit/'.$encounter->id : 'admin/data/encounters/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $encounter->name, ['class' => 'form-control']) !!}
</div>


<div class="form-group">
    {!! Form::label('initial prompt') !!}
    {!! Form::textarea('initial prompt', $encounter->initial_prompt, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::label('proceed prompt') !!}
    {!! Form::textarea('proceed prompt', $encounter->proceed_prompt, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::label('dont proceed prompt') !!}
    {!! Form::textarea('dont proceed prompt', $encounter->dont_proceed_prompt, ['class' => 'form-control wysiwyg']) !!}
 </div>

<h3>Rewards</h3>
<p>You can add loot tables containing any kind of currencies (both user- and character-attached), but be sure to keep track of which are being distributed! Character-only currencies cannot be given to users.</p>
@include('widgets._loot_select', ['loots' => $encounter->rewards, 'showLootTables' => true, 'showRaffles' => true])

<div class="text-right">
    {!! Form::submit($encounter->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@include('widgets._loot_select_row', ['items' => $items, 'currencies' => $currencies, 'tables' => $tables, 'raffles' => $raffles, 'showLootTables' => true, 'showRaffles' => true])

@endsection

@section('scripts')
@parent
@include('js._loot_js', ['showLootTables' => true, 'showRaffles' => true])

@endsection