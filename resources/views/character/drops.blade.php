@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Character Drops @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, "Character Drops" => $character->url.'/inventory']) !!}

@include('character._header', ['character' => $character])

<div class="row">
    <div class="col-md-6">
        <div class="text-center">
            <a href="{{ $character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists( public_path($character->image->imageDirectory.'/'.$character->image->fullsizeFileName)) ? $character->image->fullsizeUrl : $character->image->imageUrl }}" data-lightbox="entry" data-title="{{ $character->fullName }}">
            <img src="{{ $character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists( public_path($character->image->imageDirectory.'/'.$character->image->fullsizeFileName)) ? $character->image->fullsizeUrl : $character->image->imageUrl }}" class="image" />
            </a>
        </div>
        @if($character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists( public_path($character->image->imageDirectory.'/'.$character->image->fullsizeFileName)))
            <div class="text-right">You are viewing the full-size image. <a href="{{ $character->image->imageUrl }}">View watermarked image</a>?</div>
        @endif
    </div>
    <div class="col-md-6 text-center">
        <h2>
            Character Drops
            @if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
                <a href="#" class="float-right btn btn-outline-info btn-sm" id="paramsButton" data-toggle="modal" data-target="#paramsModal"><i class="fas fa-cog"></i> Admin</a>
            @endif
        </h2>

        <div class="card card-body mb-4">
            @if($drops->speciesItem || $drops->subtypeItem)
                <p>This character produces these drops, based on their species and/or subtype:</p>
                @if($drops->speciesItem)
                    <div class="row">
                    <div class="col-md align-self-center">
                            <h5>Species</h5>
                        </div>
                        <div class="col-md align-self-center">
                            @if($drops->speciesItem->has_image) <a href="{{ $drops->subtypeItem->idUrl }}"><img src="{{ $drops->speciesItem->imageUrl }}"></a><br/> @endif
                            {!! $drops->speciesItem->displayName !!} ({{ $drops->speciesQuantity }}/{{ $drops->dropData->data['frequency']['interval']}})
                        </div>
                    </div>
                @endif
                {{ $drops->speciesItem && $drops->subtypeItem ? '<hr/>' : null }}
                @if($drops->subtypeItem)
                    <div class="row">
                        <div class="col-md align-self-center">
                            <h5>{{ $character->image->subtype->name }} (Subtype)</h5>
                        </div>
                        <div class="col-md align-self-center">
                            @if($drops->subtypeItem->has_image) <a href="{{ $drops->subtypeItem->idUrl }}"><img src="{{ $drops->subtypeItem->imageUrl }}"></a><br/> @endif
                            {!! $drops->subtypeItem->displayName !!} ({{ $drops->subtypeQuantity }} every {{ $drops->dropData->data['frequency']['frequency'] > 1 ? $drops->dropData->data['frequency']['frequency'].' '.$drops->dropData->data['frequency']['interval'].'s' : $drops->dropData->data['frequency']['interval']}})
                        </div>
                    </div>
                @endif
            @else
                <p>This character isn't eligible for any drops.</p>
            @endif
        </div>

        @if($drops->speciesItem || $drops->subtypeItem)
            <div class="text-center">
                <p>
                    This character has {{ $drops->drops_available }} drop{{ $drops->drops_available != 1 ? 's' : '' }} available.<br/>
                    This character's next drop(s) will be available to collect {!! pretty_date($drops->next_day) !!}.
                </p>
            </div>
            @if(Auth::check() && Auth::user()->id == $character->user_id && $drops->drops_available > 0)
                {!! Form::open(['url' => 'character/'.$character->slug.'/drops']) !!}
                    {!! Form::submit('Collect Drop'.($drops->drops_available > 1 ? 's' : ''), ['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
            @endif
        @endif
    </div>
</div>

@if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
    <div class="modal fade" id="paramsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">[ADMIN] Adjust Drop</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/character/'.$character->slug.'/drops']) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('parameters', 'Group') !!}
                                    {!! Form::select('parameters', $drops->dropData->parameterArray, $drops->parameters, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('drops_available', 'Drops Available') !!}
                                    {!! Form::number('drops_available', $drops->drops_available, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
