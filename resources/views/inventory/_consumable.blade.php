@if ($type == 'user')
    <li class="list-group-item">
        <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#useConsumableForm"> Use Consumable</a>
        <div id="useConsumableForm" class="collapse">
            <!-- $characterFeatureIds = $character->image->features->pluck('feature_id')->toArray(); -->
            {!! Form::hidden('tag', $tag->tag) !!}

            <p>This action is not reversible. Are you sure you want to use this item?</p>

            <div class="form-group">
                {!! Form::select('character_id_affected', $characterOptionsMyo, null, ['class' => 'form-control mr-2 default character-id-affected', 'placeholder' => 'Select Character']) !!}
            </div>

            @if (array_key_exists('add_specific_trait', $tag->data) && $tag->data['add_specific_trait'])
                <div class="form-group">
                    {!! Form::select('feature_id_adding', $feature_options_adding, null, ['class' => 'form-control mr-2 default feature-select', 'placeholder' => 'Select a trait to add']) !!}
                </div>
            @endif

            @if (array_key_exists('remove_specific_trait', $tag->data) && $tag->data['remove_specific_trait'])
                <div class="form-group">
                    {!! Form::select('feature_id_removing', $feature_options_removing, null, ['class' => 'form-control mr-2 default feature-select', 'placeholder' => 'Select a trait to remove']) !!}
                </div>
            @endif

            @if (array_key_exists('reroll_specific_trait', $tag->data) && $tag->data['reroll_specific_trait'])
                <div class="form-group">
                    {!! Form::select('feature_id_rerolling', $feature_options_rerolling, null, ['class' => 'form-control mr-2 default feature-select', 'placeholder' => 'Select a trait to reroll']) !!}
                </div>
            @endif

            @if (array_key_exists('reroll_specific_species', $tag->data) && $tag->data['reroll_specific_species'])
                <div class="form-group">
                    {!! Form::select('species_id_rerolling', $species_options_rerolling, null, ['class' => 'form-control mr-2 default species-select', 'placeholder' => 'Select a species to reroll']) !!}
                </div>
            @endif

            <div class="text-right">
                {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
            </div>
        </div>
    </li>
    
    <script>
        (function() {
            const characterSelectInputs = document.querySelectorAll('.character-id-affected');
            const featureSelectRemoving = document.querySelector('select[name="feature_id_removing"]');
            const featureSelectRerolling = document.querySelector('select[name="feature_id_rerolling"]');
            const speciesSelectRerolling = document.querySelector('select[name="species_id_rerolling"]');

            for (let i = 0; i < characterSelectInputs.length; i++) {
                const characterSelect = characterSelectInputs[i];

                characterSelect.addEventListener('change', function () {
                    const characterId = this.value;

                    if (characterId) {
                        if (featureSelectRemoving || featureSelectRerolling) {
                            fetch(`/myo/${characterId}/features`)
                                .then(response => response.json())
                                .then(data => {
                                    if (featureSelectRemoving) {
                                        featureSelectRemoving.innerHTML = '<option value="">Select a trait to remove</option>';
                                        data.forEach(trait => {
                                            const option = document.createElement('option');
                                            option.value = trait.id;
                                            option.textContent = trait.name;
                                            featureSelectRemoving.appendChild(option);
                                        });
                                    }
                                    if (featureSelectRerolling) {
                                        featureSelectRerolling.innerHTML = '<option value="">Select a trait to reroll</option>';
                                        data.forEach(trait => {
                                            const option = document.createElement('option');
                                            option.value = trait.id;
                                            option.textContent = trait.name;
                                            featureSelectRerolling.appendChild(option);
                                        });
                                    }
                                });
                        }

                        if (speciesSelectRerolling) {
                            fetch(`/myo/${characterId}/specieses`)
                                .then(response => response.json())
                                .then(data => {
                                    if (speciesSelectRerolling) {
                                        speciesSelectRerolling.innerHTML = '<option value="">Select a species to reroll</option>';
                                        data.forEach(species => {
                                            const option = document.createElement('option');
                                            option.value = species.id;
                                            option.textContent = species.name;
                                            speciesSelectRerolling.appendChild(option);
                                        });
                                    }
                                });
                        }
                    } else {
                        if (featureSelectRemoving) {
                            featureSelectRemoving.innerHTML = '<option value="">Select a trait to remove</option>';
                        }
                        if (featureSelectRerolling) {
                            featureSelectRerolling.innerHTML = '<option value="">Select a trait to reroll</option>';
                        }
                        if (speciesSelectRerolling) {
                            speciesSelectRerolling.innerHTML = '<option value="">Select a species to reroll</option>';
                        }
                    }
                });
            }
        })();
    </script>
@endif