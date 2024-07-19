@extends('layouts.app')

@section('title')
    Forum :: Create Thread in {{ $forum->name }}
@endsection

@section('content')
    {!! breadcrumbs(['Forum' => 'forum', $forum->name => 'forum/' . $forum->id, 'Create New Thread' => 'forum/' . $forum->id . '/new']) !!}
    <h1>Create Thread in {!! $forum->displayName !!}</h1>

    @php
        $model = $forum;
    @endphp

    @auth
        <div class="card mt-3">
            <div class="card-body">
                {!! Form::open(['url' => 'comments/make/' . base64_encode(urlencode(get_class($model))) . '/' . $model->getKey()]) !!}
                <input type="hidden" name="type" value="{{ isset($type) ? $type : null }}" />
                <div class="form-group">
                    {!! Form::label('title', 'Title') !!} {!! add_help('Enter a title relevant to your thread.') !!}
                    {!! Form::text('title', Request::get('title'), ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('message', 'Enter your message here:') !!}
                    {!! Form::textarea('message', null, ['class' => 'form-control ' . (config('lorekeeper.settings.wysiwyg_comments') ? 'comment-wysiwyg' : ''), 'rows' => 5, config('lorekeeper.settings.wysiwyg_comments') ? '' : 'required']) !!}
                    <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>
                </div>

                {!! Form::submit('Submit', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
                {!! Form::close() !!}
            </div>
        </div>
        <br />
    @else
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Authentication required</h5>
                <p class="card-text">You must log in to post a comment.</p>
                <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
            </div>
        </div>
    @endauth
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: '.comment-wysiwyg',
                height: 250,
                menubar: false,
                convert_urls: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen spoiler',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | code',
                content_css: [
                    '{{ asset('css/app.css') }}',
                    '{{ asset('css/lorekeeper.css') }}'
                ],
                spoiler_caption: 'Toggle Spoiler',
                target_list: false
            });
        });
    </script>
@endsection
