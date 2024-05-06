<h1>Encounter Potion Settings</h1>

<h3>Basic Information</h3>
<p>This is how much energy a {{ config('lorekeeper.encounters.use_characters') ? 'character' : 'user' }} will recover when using this item.</p>
<p>This tag does not grant currency, if set in config.</p>
<div class="form-group">
    {!! Form::label('Value') !!}
    {!! Form::number('value', $tag->getData()['value'], ['class' => 'form-control']) !!}
</div>
