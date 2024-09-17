<h2 class="text-center">Consumable Item Settings</h2>

<br/>

<h3>Trait adding</h3>

<div class="form-group">
    {!! Form::label('Add trait (Optional)') !!} {!! add_help('If selected, this will add the selected trait when the item is used.') !!}
    {!! Form::select('trait_added', $trait_added, $tag->getData()['trait_added'], ['class' => 'form-control', 'id' => 'trait_added']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('add_specific_trait', 1, $tag->getData()['add_specific_trait'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'add_specific_trait']) !!}
    {!! Form::label('add_specific_trait', 'User choses trait?', ['class' => 'form-check-label ml-3']) !!}
</div>

<br/>

<h3>Trait removing</h3>

<div class="form-group">
    {!! Form::label('Remove trait (Optional)') !!} {!! add_help('If selected, this will remove the selected trait when the item is used.') !!}
    {!! Form::select('trait_removed', $trait_removed, $tag->getData()['trait_removed'], ['class' => 'form-control', 'id' => 'trait_removed']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('remove_specific_trait', 1, $tag->getData()['remove_specific_trait'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'remove_specific_trait']) !!}
    {!! Form::label('remove_specific_trait', 'User chooses trait?', ['class' => 'form-check-label ml-3']) !!}
</div>

<br/>

<h3>Trait rerolling</h3>

<div class="form-group">
    {!! Form::checkbox('reroll_traits', 1, $tag->getData()['reroll_traits'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'reroll_traits']) !!}
    {!! Form::label('reroll_traits', 'Reroll traits?', ['class' => 'form-check-label ml-3']) !!}
</div>

<br/>
<hr/>