@if($hunt)
    {!! Form::open(['url' => 'admin/data/hunts/delete/'.$hunt->id]) !!}

    <p>You are about to delete the scavenger hunt <strong>{{ $hunt->name }}</strong>. This is not reversible. If users have participated in this hunt, you will not be able to delete it.</p>
    <p>Are you sure you want to delete <strong>{{ $hunt->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Scavenger Hunt', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid scavenger hunt selected.
@endif