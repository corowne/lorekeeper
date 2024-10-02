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
                <th></th>
            </tr>
        </thead>
        <tbody id="sortable" class="sortable">
            @foreach ($eventIcons as $eventIcon)
            <tr class="sort-item" data-id="{{ $eventIcon->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        <a href="">{{ $eventIcon->image }}</a>
                    </td>
                    <td>
                        <a href="">{{ $eventIcon->link }}</a>
                    </td>
                    <td>
                        <a href="">{{ $eventIcon->alt_text }}</a>
                    </td>
                    <td class="text-right">
                        <a href="#" class="btn btn-outline-primary btn-sm edit-event-icon" data-id="{{ $eventIcon->id }}">Edit</a>
                        <a href="#" class="btn btn-outline-danger btn-sm delete-event-icon" data-id="{{ $eventIcon->id }}">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/data/event-icon/sort']) !!}
        {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
        {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
    @parent
    @if (isset($eventIcon))
        <script>
            $(document).ready(function() {
                $('.delete-event-icon').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/data/event-icon/delete/') }}" + "/" + this.getAttribute('data-id'), 'Delete Icon');
                });

                $('.edit-event-icon').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/data/event-icon/edit/') }}" + "/" + this.getAttribute('data-id'), 'Edit Icon');
                });

                $("#sortable").sortable({
                items: '.sort-item',
                handle: ".handle",
                placeholder: "sortable-placeholder",
                stop: function(event, ui) {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                },
                create: function() {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                }
                });
                $("#sortable").disableSelection();
            });
        </script>
    @endif
@endsection
