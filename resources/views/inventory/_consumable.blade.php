@if ($type != 'user')
    <li class="list-group-item">
        <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#useConsumableForm"> Use Consumable</a>
        <div id="useConsumableForm" class="collapse">
            {!! Form::hidden('tag', $tag->tag) !!}
            <p>This action is not reversible. Are you sure you want to use this item?</p>

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

            <div class="text-right">
                {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
            </div>
        </div>
    </li>
@endif