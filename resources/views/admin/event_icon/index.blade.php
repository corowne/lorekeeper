@extends('admin.layout')

@section('admin-title')
    File Manager
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'EventIcon' => 'admin/data/event-icon']) !!}

    <h1>Event Icon Manager</h1>

    <p>This page allows you to upload event icons.</p>
    <p>Currently will only display the first uploaded one. If you need to display another you have to delete the others. Will come back with visibility toggles soon!</p>


    {!! Form::open(['url' => 'admin/data/event-icon/create', 'files' => true]) !!}

    <div class="p-4">
        <div class="form-group">
            {!! Form::label('link') !!}
            {!! Form::text('link', '', ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('alt text') !!} {!! add_help('This is for accessibility purposes.') !!}
            {!! Form::text('alt_text', '', ['class' => 'form-control']) !!}
        </div>

        {{-- <div class="form-group">
            {!! Form::checkbox('is_visible', 1, $eventIcon->id ? $eventIcon->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_visible', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the shop will not be visible to regular users.') !!}
        </div> --}}

        <div class="form-group">
            {!! Form::label('Image') !!}
            <div>{!! Form::file('image') !!}</div>
        </div>

        <div class="text-right">
            {!! Form::submit('Create', ['class' => 'btn btn-primary']) !!}
        </div>

        {!! Form::close() !!}
    </div>

    <table class="table table-sm">
        <thead>
            <tr>
                <th>Image</th>
                <th>Link</th>
                <th>Alt Text</th>
                <th>Visibility</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($eventIcons as $eventIcon)
                <tr>
                    <td>
                        <a href="">{{ $eventIcon->image }}</a>
                    </td>
                    <td>
                        <a href="">{{ $eventIcon->link }}</a>
                    </td>
                    <td>
                        <a href="">{{ $eventIcon->alt_text }}</a>
                    </td>
                    {{-- <td>
                        @if ($eventIcon)
                            {!! Form::open(['url' => 'admin/data/event-icon/']) !!}
                            <div class="form-group">
                                {!! Form::checkbox('is_visible', 1, $eventIcon->id ? $eventIcon->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                                {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Toggle for visibility of icon') !!}
                            </div>
                        @endif --}}
                    </td>
                    <td class="text-right">
                        {{-- {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!} --}}
                        <a href="#" class="btn btn-outline-danger btn-sm delete-eventicon"
                            data-name="{{ $eventIcon }}">Delete</a>
                            {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
    @parent
    @if (isset($eventIcon))
        <script>
            $(document).ready(function() {
                $('.delete-eventicon').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/data/event-icon/delete') }}/{{ $eventIcon->id }}",
                        'Delete EventIcon');
                });
            });
        </script>
    @endif
@endsection
