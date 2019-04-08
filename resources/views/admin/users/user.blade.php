@extends('admin.layout')

@section('admin-title') User Index @stop

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'User Index' => 'admin/users', $user->name => 'admin/user/'.$user->name]) !!}

<h1>User: {!! $user->displayName !!}</h1>

<h3>Basic Info</h3>
{!! Form::open(['url' => 'admin/users/edit/basic/'.$user->name]) !!}
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Username</label>
        <div class="col-md-10">
            {!! Form::text('name', $user->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Rank 
            @if($user->isAdmin)
                {!! add_help('The rank of the admin user cannot be edited.') !!}
            @elseif(!Auth::user()->canEditRank($user->rank))
                add_help('Your rank is not high enough to edit this user.')
            @endif
        </label>
        <div class="col-md-10">
            @if(!$user->isAdmin && Auth::user()->canEditRank($user->rank)
                {!! Form::select('rank_id', $ranks, Request::get('rank_id'), ['class' => 'form-control']) !!}
            @else
                {!! Form::text('rank_id', $ranks[$user->rank_id], ['class' => 'form-control', 'disabled']) !!}
            @endif
        </div>
    </div>
    <div class="text-right">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}

<h3>Alias</h3>
<p>As users are supposed to verify that they own the deviantART account themselves, aliases cannot be edited directly. If a user wants to change their alias, clear it here and ask them to go through the verification process again while logged into their new account.</p>
<div class="form-group row">
    <label class="col-md-2 col-form-label">Alias</label>
    <div class="col-md-10">
        {!! Form::text('alias', $user->alias, ['class' => 'form-control', 'disabled']) !!}
    </div>
</div>
{!! Form::open(['url' => 'admin/users/edit/alias/'.$user->name]) !!}
    <div class="text-right">{!! Form::submit('Clear Alias', ['class' => 'btn btn-danger']) !!}</div>
{!! Form::close() !!}

<h3>Account</h3>

{!! Form::open(['url' => 'admin/users/account/'.$user->name]) !!}
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Email Address</label>
        <div class="col-md-10">
            {!! Form::text('email', $user->email, ['class' => 'form-control', 'disabled']) !!}
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Join Date</label>
        <div class="col-md-10">
            {!! Form::text('created_at', format_date($user->created_at), ['class' => 'form-control', 'disabled']) !!}
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Is an FTO {!! add_help('FTO (First Time Owner) means that they have no record of possessing a character from this world. This status is automatically updated when they earn their first character, but can be toggled manually in case off-record transfers have happened before.') !!}</label>
        <div class="col-md-10">
            <div class="form-check form-control-plaintext">
                {!! Form::checkbox('is_fto', 1, $user->settings->is_fto, ['class' => 'form-check-input', 'id' => 'checkFTO']) !!}
            </div>
            
        </div>
    </div>
    <div class="text-right">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}
@endsection