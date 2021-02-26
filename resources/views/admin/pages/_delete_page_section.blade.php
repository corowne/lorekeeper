@if($section)
    {!! Form::open(['url' => 'admin/page-sections/delete/'.$section->id]) !!}

    <p>You are about to delete the section <strong>{{ $section->name }}</strong>. This is not reversible. If categories in this section exist, they will be deleted but not removed.</p>
    <p>Are you sure you want to delete <strong>{{ $section->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Page Section', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid section selected.
@endif