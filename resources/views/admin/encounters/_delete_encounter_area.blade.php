@if($area)
    {!! Form::open(['url' => 'admin/data/encounters/areas/delete/'.$area->id]) !!}

    <p>You are about to delete the area <strong>{{ $area->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $area->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Area', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid area selected.
@endif