@extends('admin.layout')

@section('admin-title') Grant EXP @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Grant EXP' => 'admin/grants/exp']) !!}

<h1>Grant EXP</h1>

{!! Form::open(['url' => 'admin/grants/exp']) !!}

<h3>Basic Information</h3>
<div class="form-group">
    {!! Form::label('names[]', 'Username(s)') !!} {!! add_help('You can select up to 10 users at once.') !!}
    {!! Form::select('names[]', $users, null, ['id' => 'usernameList', 'class' => 'form-control', 'multiple']) !!}
</div>

<div class="form-group">
    {!! Form::label('Quantity') !!}
    {!! Form::number('quantity', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
</div>

<h3>Additional Data</h3>

<div class="form-group">
    {!! Form::label('data', 'Reason (Optional)') !!} {!! add_help('A reason for the grant. This will be noted in the logs and in the inventory description.') !!}
    {!! Form::text('data', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
</div>

<div class="text-right">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $('#usernameList').selectize({
            maxItems: 10
        });
    });
</script>
@endsection