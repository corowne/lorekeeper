@if($encounter)
    {!! Form::open(['url' => 'admin/data/encounters/delete/'.$encounter->id]) !!}

    <p>You are about to delete the encounter <strong>{{ $encounter->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $encounter->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Encounter', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid encounter selected.
@endif