@extends('home.layout')

@section('home-title') My MYO Slots @endsection

@section('home-content')
{!! breadcrumbs(['Characters' => 'characters', 'My MYO Slots' => 'myos']) !!}

<h1>
    My MYO Slots
</h1>

<p>This is a list of MYO slots you own - click on a slot to view details about it. MYO slots can be submitted for design approval from their respective pages.</p>
<div class="row">
    @foreach($slots as $slot)
        <div class="col-md-3 col-6 text-center mb-2">
            <div>
                <a href="{{ $slot->url }}"><img src="{{ $slot->image->thumbnailUrl }}" class="img-thumbnail" alt="Thumbnail for {{ $slot->fullName }}" /></a>
            </div>
            <div class="mt-1 h5">
                {!! $slot->displayName !!}
            </div>
        </div>
    @endforeach
</div>
@endsection
