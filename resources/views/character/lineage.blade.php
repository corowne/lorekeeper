@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Profile @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
    @if($character->is_myo_slot)
    {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Profile' => $character->url . '/profile']) !!}
    @else
    {!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, 'Lineage' => $character->url . '/lineage']) !!}
    @endif

    @include('character._header', ['character' => $character])
    <?php
        $descendants = [
            "Children" => $children,
            "Grandchildren" => $grandchildren,
            "Great-Grandchildren" => $greatGrandchildren,
        ];
    ?>
    @foreach ($descendants as $typeOf => $children)
        <h3>
            <a href="{{ $character->url.'/'.strtolower($typeOf) }}">{{ $typeOf }}</a>
        </h3>
        @if(!$children || count($children) == 0)
            <p>Doesn't have any {{ $typeOf }}.</p>
        @else
            <div class="row mb-4">
                @foreach($children as $child)
                    <div class="col-md-3 col-6 text-center">
                        <div>
                            <a href="{{ $child->url }}"><img src="{{ $child->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                        </div>
                        <div class="mt-1">
                            <a href="{{ $child->url }}" class="h5 mb-0">{{ $child->fullName }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="text-right"><a href="{{ $character->url.'/'.strtolower($typeOf) }}">View all...</a></div>
        <hr>
    @endforeach
@endsection
