@extends('encounters.layout')

@section('title')
    Encounter Areas
@endsection

@section('content')
    {!! breadcrumbs(['Encounter Areas' => 'encounter-areas']) !!}

    <div class="d-flex flex-wrap">
        <div class="col-md-5 col-6">
            <h1>Encounter Areas</h1>
            <p>Here is a list of areas that you can venture into. You will recieve a randomized encounter and options of
                what to do
                in it.</p>
            <p>You have limited energy to explore each day, so spend it wisely.</p>
        </div>
        <div class="col-md-6 col-6">
            @if ($use_characters)
                <div class="col-md-6 justify-content-center text-center">
                    <h3>Current Character</h3>
                    @if (!$user->settings->encounterCharacter)
                        <p>No character selected!</p>
                    @else
                        @php
                            $character = $user->settings->encounterCharacter;
                            if ($use_energy) {
                                $energy = $character->encounter_energy;
                            } else {
                                $energy = \App\Models\Character\CharacterCurrency::where('character_id', $character->id)
                                    ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                                    ->first()->quantity;
                            }
                        @endphp

                        <div>
                            <a href="{{ $character->url }}">
                                <img src="{{ $character->image->thumbnailUrl }}" style="width: 150px;" class="img-thumbnail" />
                            </a>
                        </div>
                        <div class="mt-1">
                            <a href="{{ $character->url }}" class="h5 mb-0">
                                {{ $character->fullName }}
                            </a>
                            <p>{{ $character->fullName }} has <strong>{{ $energy }}</strong> energy.</p>
                        </div>
                    @endif
                    {!! Form::open(['url' => 'encounter-areas/select-character']) !!}
                    {!! Form::select('character_id', $characters, $user->settings->encounter_character_id, [
                        'class' => 'form-control m-1',
                        'placeholder' => 'None Selected',
                    ]) !!}
                    {!! Form::submit('Select Character', ['class' => 'btn btn-primary mb-2']) !!}
                    {!! Form::close() !!}
                </div>
            @else
                @php
                    $user = Auth::user();
                    if ($use_energy) {
                        $energy = $user->settings->encounter_energy;
                    } else {
                        $energy = \App\Models\User\UserCurrency::where('user_id', $user->id)
                            ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                            ->first()->quantity;
                    }
                @endphp
                You have <strong>{{ $energy }}</strong> energy.
            @endif
        </div>
    </div>

    @if (!count($areas))
        <div class="alert alert-info">No areas found. Check back later!</div>
    @else
        <div class="row shops-row">
            @foreach ($areas as $area)
                @include('encounters._area_entry')
            @endforeach
        </div>
    @endif
@endsection
