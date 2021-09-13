@extends('layouts.app')

@section('title') Add Your Birthday @endsection

@section('content')

<h1>Add Birthday</h1>
<p>Your account does not have a birth date. To gain access to personalised site features, you must add a birthdate to your {{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} account. Your birthday is used to verify if you are allowed to access this site.
<br>It is private by default.</p>
<p><strong>Please make sure you enter the correct date.</strong></p>

{!! Form::open(['url' => '/birthday']) !!}
<div class="form-group row">
    {{ Form::label('dob', 'Date of Birth', ['class' => 'col-md-4 col-form-label text-md-right']) }}
        <div class="col-md-6">
            <div class="col-md row">
            {{ Form::selectRange('dob[day]', 1, 31, null, ['class' => 'form-control col-2 mr-1']) }}
            {{ Form::selectMonth('dob[month]', null, ['class' => 'form-control col-2 mr-1']) }}
            {{ Form::selectYear('dob[year]', date('Y'), date('Y') - 70, null, ['class' => 'form-control col-2']) }}
        </div>
    </div>
    <div class="text-right">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>
</div>
{!! Form::close() !!}

@endsection