<h1>Consumable Item Settings</h1>

<div class="form-group">
    {!! Form::label('Add trait (Optional)') !!} {!! add_help('If selected, this will add the selected trait when the item is used.') !!}
    {!! Form::select('trait_added', $trait_added, $tag->getData()['trait_added'], ['class' => 'form-control', 'id' => 'trait_added']) !!}
</div>

<div class="form-group">
    {!! Form::label('Remove trait (Optional)') !!} {!! add_help('If selected, this will remove the selected trait when the item is used.') !!}
    {!! Form::select('trait_removed', $trait_removed, $tag->getData()['trait_removed'], ['class' => 'form-control', 'id' => 'trait_removed']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('reroll_traits', 1, $tag->getData()['reroll_traits'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'resellable']) !!}
    {!! Form::label('reroll_traits', 'Reroll traits?', ['class' => 'form-check-label ml-3']) !!}
</div>
