@extends('admin.layout')

@section('admin-title')
    {{ $news->id ? 'Edit' : 'Create' }} News Post
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'News' => 'admin/news', ($news->id ? 'Edit' : 'Create') . ' Post' => $news->id ? 'admin/news/edit/' . $news->id : 'admin/news/create']) !!}

    <h1>{{ $news->id ? 'Edit' : 'Create' }} News Post
        @if ($news->id)
            <a href="#" class="btn btn-danger float-right delete-news-button">Delete Post</a>
        @endif
    </h1>

    {!! Form::open(['url' => $news->id ? 'admin/news/edit/' . $news->id : 'admin/news/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Title') !!}
                {!! Form::text('title', $news->title, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Post Time (Optional)') !!} {!! add_help('This is the time that the news post should be posted. Make sure the Is Viewable switch is off.') !!}
                {!! Form::text('post_at', $news->post_at, ['class' => 'form-control datepicker']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Post Content') !!}
        {!! Form::textarea('text', $news->text, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_visible', 1, $news->id ? $news->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_visible', 'Is Viewable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, the post will not be visible. If the post time is set, it will automatically become visible at/after the given post time, so make sure the post time is empty if you want it to be completely hidden.') !!}
            </div>
        </div>
        @if ($news->id && $news->is_visible)
            <div class="col-md">
                <div class="form-group">
                    {!! Form::checkbox('bump', 1, null, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('bump', 'Bump News', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If toggled on, this will alert users that there is new news. Best in conjunction with a clear notification of changes!') !!}
                </div>
            </div>
        @endif
    </div>

    <div class="text-right">
        {!! Form::submit($news->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    @include('widgets._datetimepicker_js')
    <script>
        $(document).ready(function() {
            $('.delete-news-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/news/delete') }}/{{ $news->id }}", 'Delete Post');
            });
        });
    </script>
@endsection
