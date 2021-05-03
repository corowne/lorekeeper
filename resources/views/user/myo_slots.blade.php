@extends('user.layout')

@section('profile-title') {{ $user->name }}'s MYO Slots @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'MYO Slots' => $user->url . '/myos']) !!}

<h1>
    {!! $user->displayName !!}'s MYO Slots
</h1>

@if($myos->count())
    <div class="row">
        @foreach($myos as $myo)
            <div class="col-md-3 col-6 text-center mb-2">
                <div>
                    <a href="{{ $myo->url }}"><img src="{{ $myo->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                </div>
                <div class="mt-1 h5">
                    @if(!$myo->is_visible) <i class="fas fa-eye-slash"></i> @endif {!! $myo->displayName !!}
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No MYO slots found.</p> 
@endif

@endsection
