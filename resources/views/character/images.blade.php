@extends('character.layout')

@section('profile-title') {{ $character->fullName }}'s Images @endsection

@section('profile-content')
{!! breadcrumbs(['Masterlist' => 'masterlist', $character->fullName => $character->url]) !!}

@include('character._header', ['character' => $character])

<div class="tab-content">
    @foreach($character->images as $image)
        <div class="tab-pane fade {{ $image->id == $character->character_image_id ? 'show active' : '' }}" id="image-{{ $image->id }}">
            <div class="row mb-3">
                <div class="text-center col-md-7">
                    <a href="{{ $image->imageUrl }}" data-lightbox="entry" data-title="{{ $character->fullName }} [#{{ $image->id }}]">
                        <img src="{{ $image->imageUrl }}" class="image" />
                    </a>
                </div>
                @include('character._image_info', ['image' => $image])
            </div>
        </div>
    @endforeach
</div>

<h3>
    Images
    @if(Auth::check() && Auth::user()->hasPower('manage_masterlist'))
        <a href="{{ url('admin/character/'.$character->slug.'/image') }}" class="float-right btn btn-outline-info btn-sm"><i class="fas fa-plus"></i> Add Image</a>
    @endif
</h3>

<ul class="row nav image-nav mb-2" id="sortable">
    @foreach($character->images as $image)
        <li class="col-md-3 col-6 text-center nav-item sort-item" data-id="{{ $image->id }}">
            <a id="thumbnail-{{ $image->id }}" data-toggle="tab" href="#image-{{ $image->id }}" role="tab" class="{{ $image->id == $character->character_image_id ? 'active' : '' }}"><img src="{{ $image->thumbnailUrl }}" class="img-thumbnail" /></a>
        </li>
    @endforeach
</ul>
{!! Form::open(['url' => 'admin/character/' . $character->slug . '/images/sort', 'class' => 'text-right']) !!}
{!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
{!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
{!! Form::close() !!}

@endsection
@section('scripts')
    @include('character._image_js')
    <script>
        $( document ).ready(function() {
            $( "#sortable" ).sortable({
                characters: '.sort-item',
                placeholder: "sortable-placeholder",
                stop: function( event, ui ) {
                    $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
                },
                create: function() {
                    $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
                }
            });
            $( "#sortable" ).disableSelection();
        });
    </script>
@endsection
