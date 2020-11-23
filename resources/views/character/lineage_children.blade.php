@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Profile @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
    @if($character->is_myo_slot)
    {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Profile' => $character->url . '/profile']) !!}
    @else
    {!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, $title => $character->url . '/' . strtolower($title)]) !!}
    @endif

    @include('character._header', ['character' => $character])
    <h3>{{ $title }}</h3>
    @if (!$children || $children->count() == 0)
        <p>Doesn't have any {{ $title }}.</p>
    @else
        <div class="row">
            @foreach($children as $child)
                <div class="col-md-3 col-6 text-center mb-2">
                    <div>
                        <a href="{{ $child->url }}"><img src="{{ $child->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                    </div>
                    <div class="mt-1 h5">
                        {!! $child->displayName !!}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
