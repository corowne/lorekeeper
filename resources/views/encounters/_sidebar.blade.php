<ul>
    <li class="sidebar-header"><a href="{{ url('encounter-areas') }}" class="card-link">Encounter Stats</a></li>
    <li class="sidebar-section">
        <div class="sidebar-item">
            @if ($use_characters)
                <div style="margin: 10px;" class="justify-content-center text-center">
                    <h5>Current Character</h5>
                    @if (!$character)
                        <p>No character selected!</p>
                    @else
                        <div>
                            <a href="{{ $character->url }}">
                                <img src="{{ $character->image->thumbnailUrl }}" style="width: 150px;" class="img-thumbnail" />
                            </a>
                        </div>
                        <div class="mt-1">
                            <a href="{{ $character->url }}" class="h5 mb-0">
                                {{ $character->fullName }}
                            </a>
                            <p>{{ $character->fullName }} has <strong class="energy-amount">{{ $energy }}</strong> energy.</p>
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
                <div class="justify-content-center text-center">You have <strong class="energy-amount">{{ $energy }}</strong> energy.</div>
            @endif
        </div>
    </li>
</ul>
