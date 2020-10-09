@extends('admin.layout')

@section('admin-title') Sub Masterlists @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Sub Masterlists' => 'admin/data/sublists', ($sublist->id ? 'Edit' : 'Create').' Sub Masterlist' => $sublist->id ? 'admin/data/sublists/edit/'.$sublist->id : 'admin/data/sublists/create']) !!}

<h1>{{ $sublist->id ? 'Edit' : 'Create' }} Sub Masterlist
    @if($sublist->id)
        <a href="#" class="btn btn-danger float-right delete-feature-button">Delete Sub Masterlist</a>
    @endif
</h1>

{!! Form::open(['url' => $sublist->id ? 'admin/data/sublists/edit/'.$sublist->id : 'admin/data/sublists/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $sublist->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Key') !!}
            {!! Form::text('key', $sublist->key, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::checkbox('show_main', 1, $sublist->id ? $sublist->show_main : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('show_main', 'Show on Main', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Turn on to include these characters in the main masterlist as well. Turn off to entirely seperate them into the sub masterlist.') !!}
</div>

<div class="text-right">
    {!! Form::submit($sublist->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-sublist-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/sublist/delete') }}/{{ $sublist->id }}", 'Delete Sub Masterlist');
    });
});
    
</script>
@endsection