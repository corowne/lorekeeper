<li class="list-group-item">
    <a class="card-title h5 collapse-title" data-toggle="collapse" href="#openEncounterPotionForm"> Use Encounter Potion</a>
    <div id="openEncounterPotionForm" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        <div class="alert alert-info mt-2">
            This potion will add
            {{ $tag->getData()['value'] }} to {{config('lorekeeper.encounters.use_characters') ? 'a character\'s' : 'your' }} encounter energy.
        </div>
        <p>This action is not reversible. Are you sure you want to use this item?</p>
        @if (config('lorekeeper.encounters.use_characters'))
            <div class="form-group">
                {!! Form::label('Character') !!}
                {!! Form::select('energy_recipient', $stack->first()->user->characters()->myo(0)->get()->pluck('fullName', 'id')->toArray(), null, ['class' => 'form-control', 'placeholder' => 'Select a Character']) !!}
            </div>
        @endif
        <div class="text-right">
            {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
