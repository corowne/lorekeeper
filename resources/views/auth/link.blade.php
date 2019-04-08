@extends('layouts.app')

@section('title') Link Your dA Account @endsection

@section('content')
<h1>Link dA Account</h1>
<p>Your account does not have a deviantART account linked to it. To gain access to personalised site features, you must link your deviantART account to your {{ config('app.name', 'Lorekeeper') }} account.</p>

<div class="alert alert-warning">You cannot unlink or change your dA account after this process has been completed. Please make sure you are logged into the correct account before continuing.</div>

<div class="text-center"><a class="btn btn-primary" href="{{ url()->current().'?login=DeviantArt' }}">Link deviantART Account</a></div>
@endsection
