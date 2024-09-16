@if ($type != 'user')
    <li class="list-group-item">
        <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#useConsumableForm"> Use Consumable</a>
        <div id="useConsumableForm" class="collapse">
            {!! Form::hidden('tag', $tag->tag) !!}
            <p>This action is not reversible. Are you sure you want to use this item?</p>

            <div class="text-right">
                {!! Form::button('Use', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
            </div>
        </div>
    </li>
@endif