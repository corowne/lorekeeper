<li class="list-group-item">
    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#openBoxForm"> Open Box</a>
    {!! Form::open(['url' => 'inventory/act/'.$stack->id.'/'.$tag->tag, 'id' => 'openBoxForm', 'class' => 'collapse']) !!}
        <p>This action is not reversible. Are you sure you want to open this box?</p>
        <div class="text-right">
            {!! Form::submit('Open', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</li>