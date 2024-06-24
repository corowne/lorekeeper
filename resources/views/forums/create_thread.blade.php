@extends('layouts.app')

@section('title') Forum :: Create Thread in {{ $forum->name }} @endsection

@section('content')
{!! breadcrumbs(['Forum' => 'forum' , $forum->name => 'forum/'.$forum->id, 'Create New Thread' => 'forum/'.$forum->id.'/new' ]) !!}
<h1>Create Thread in {!! $forum->displayName !!}</h1>

@php
$model = $forum;
@endphp

@auth
    @include('comments._form')
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
