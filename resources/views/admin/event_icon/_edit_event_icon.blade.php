@if ($eventIcon)
    {!! Form::open(['url' => 'admin/data/event-icon/edit/' . $eventIcon->id, 'files' => true, 'method' => 'post']) !!}

    <div class="form-group">
            {!! Form::label('link') !!}
            {!! Form::text('link', $eventIcon->link, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('alt text') !!} {!! add_help('This is for accessibility purposes.') !!}
            {!! Form::text('alt_text', $eventIcon->alt_text, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Image') !!}
            <div>{!! Form::file('image') !!}</div>
        </div>

        <div class="form-group">
            {!! Form::checkbox('is_visible', 1, $eventIcon->is_visible, ['class' => 'form-check-input mr-2', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_visible', 'Set Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, this event icon image will not be visible.') !!}
        </div>

    <div class="text-right">
        {!! Form::submit('Edit Event Icon', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid event icon selected.
@endif
