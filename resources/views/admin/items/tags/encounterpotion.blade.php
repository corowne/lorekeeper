<h1>Encounter Potion Settings</h1>

<h3>Basic Information</h3>
<p>This is how much energy a user will recover when using this item.</p>
<div class="form-group">
    {!! Form::label('Value') !!}
    {!! Form::number('value', $tag->getData()['value'], ['class' => 'form-control']) !!}
</div>
