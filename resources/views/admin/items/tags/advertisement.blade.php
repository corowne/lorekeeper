<h2 class="text-center">Advertisement Item Settings</h2>

<br/>

<div class="form-group">
    {!! Form::checkbox('choose_species', 1, $tag->getData()['choose_species'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'choose_species']) !!}
    {!! Form::label('choose_species', 'User choses a species', ['class' => 'form-check-label ml-3']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('choose_trait', 1, $tag->getData()['choose_trait'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'choose_trait']) !!}
    {!! Form::label('choose_trait', 'User chooses a trait', ['class' => 'form-check-label ml-3']) !!}
</div>

<br/>
<hr/>