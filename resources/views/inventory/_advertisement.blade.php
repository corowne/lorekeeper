@if ($type == 'user')
    <li class="list-group-item">
        <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#useAdvertisementForm">Use Advertisement</a>
        <div id="useAdvertisementForm" class="collapse">
            {!! Form::hidden('tag', $tag->tag) !!}

            <p>This action is not reversible. Are you sure you want to use this item?</p>

            @if ($tag->data['method'] == 'choose_either')
                <div class="form-group">
                    {!! Form::select('setting_species_or_trait', ['species' => 'Species', 'trait' => 'Trait'], null, ['class' => 'form-control mr-2 default species-select setting-species-or-trait', 'placeholder' => 'Do you want to set the species or the trait?']) !!}
                </div>
            @endif

            @if (in_array($tag->data['method'], ['choose_both', 'choose_either', 'choose_species']))
                <div class="form-group form-group-choose-species">
                    {!! Form::select('species_id_adding', $species_options_adding, null, ['class' => 'form-control mr-2 default species-select', 'placeholder' => 'Select a species to add']) !!}
                </div>
            @endif

            @if (in_array($tag->data['method'], ['choose_both', 'choose_either', 'choose_trait']))
                <div class="form-group form-group-choose-trait">
                    {!! Form::select('feature_id_adding', $feature_options_adding, null, ['class' => 'form-control mr-2 default feature-select', 'placeholder' => 'Select a trait to add']) !!}
                </div>
            @endif

            <div class="text-right">
                {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
            </div>
        </div>
    </li>
    
    <script>
        (function() {
            const settingSpeciesOrTraitInput = document.querySelectorAll('.setting-species-or-trait');
            const formGroupSpeciesSelect = document.querySelector('.form-group-choose-species');
            const formGroupTraitSelect = document.querySelector('.form-group-choose-trait');

            @if (in_array($tag->data['method'], ['choose_neither', 'choose_either']))
                formGroupSpeciesSelect?.classList.add('hidden');
                formGroupTraitSelect?.classList.add('hidden');
            @elseif (in_array($tag->data['method'], ['choose_species']))
                formGroupSpeciesSelect?.classList.remove('hidden');
                formGroupTraitSelect?.classList.add('hidden');
            @elseif (in_array($tag->data['method'], ['choose_trait']))
                formGroupSpeciesSelect?.classList.add('hidden');
                formGroupTraitSelect?.classList.remove('hidden');
            @elseif (in_array($tag->data['method'], ['choose_both']))
                formGroupSpeciesSelect?.classList.remove('hidden');
                formGroupTraitSelect?.classList.remove('hidden');
            @endif

            for (let i = 0; i < settingSpeciesOrTraitInput.length; i++) {
                const chooseInput = settingSpeciesOrTraitInput[i];

                chooseInput.addEventListener('change', function () {
                    const optionSelected = this.value;

                    switch (optionSelected) {
                        case 'species':
                            formGroupSpeciesSelect?.classList.remove('hidden');
                            formGroupTraitSelect?.classList.add('hidden');
                            break;
                        case 'trait':
                            formGroupSpeciesSelect?.classList.add('hidden');
                            formGroupTraitSelect?.classList.remove('hidden');
                            break;
                        default:
                            formGroupSpeciesSelect?.classList.add('hidden');
                            formGroupTraitSelect?.classList.add('hidden');
                            break;
                    }
                });
            }
        })();
    </script>
@endif