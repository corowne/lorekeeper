<h2 class="text-center">Advertisement Item Settings</h2>

<br/>

<div class="form-group">
    {!! Form::label('Application method') !!} {!! add_help('Defines what properties the user can set when using the advertisement item.') !!}
    {!! Form::select('method', $method, $tag->getData()['method'], ['class' => 'form-control', 'id' => 'method']) !!}
</div>

<br/>
<hr/>