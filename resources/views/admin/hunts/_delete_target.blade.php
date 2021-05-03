@if($target)
    {!! Form::open(['url' => 'admin/data/hunts/targets/delete/'.$target->id]) !!}

    <p>You are about to delete this target. This is not reversible. If users have participated in the parent hunt, you will not be able to delete the target.</p>
    <p>Are you sure you want to delete this target?</p>

    <div class="text-right">
        {!! Form::submit('Delete Target', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid target selected.
@endif