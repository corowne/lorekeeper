@extends('user.layout')

@section('profile-title') {{ $user->name }}'s {{ $sublist->name }} @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, $sublist->name => $user->url . '/sublist/'.$sublist->key]) !!}

<h1>
    {!! $user->displayName !!}'s {{ $sublist->name }}
</h1>


@if($characters->count())
    <div class="row">
        @foreach($characters as $character)
            <div class="col-md-3 col-6 text-center mb-2">
                <div>
                    <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                </div>
                <div class="mt-1 h5">
                    {!! $character->displayName !!}
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No characters found.</p> 
@endif

@endsection
