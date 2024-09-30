@if ($type == 'user')
    <li class="list-group-item">
        <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#useAdvertisementForm">Use Advertisement</a>
        <div id="useAdvertisementForm" class="collapse">
            {!! Form::hidden('tag', $tag->tag) !!}

            <p>This action is not reversible. Are you sure you want to use this item?</p>

            @if (array_key_exists('choose_species', $tag->data) && $tag->data['choose_species'])
                <div class="form-group">
                    {!! Form::select('species_id_adding', $species_options_adding, null, ['class' => 'form-control mr-2 default feature-select', 'placeholder' => 'Select a species to add']) !!}
                </div>
            @endif

            @if (array_key_exists('choose_trait', $tag->data) && $tag->data['choose_trait'])
                <div class="form-group">
                    {!! Form::select('feature_id_adding', $feature_options_adding, null, ['class' => 'form-control mr-2 default feature-select', 'placeholder' => 'Select a trait to add']) !!}
                </div>
            @endif

            <div class="text-right">
                {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
            </div>
        </div>
    </li>
@endif