@extends('layouts.app')

@section('title') {{ $news->title }} @endsection

@section('content')
    {!! breadcrumbs(['Site News' => 'news', $news->title => $news->url]) !!}
    @include('news._news', ['news' => $news, 'page' => TRUE])
<hr>
<br><br>
<div class="container">
    @comments(['model' => $news,
            'perPage' => 5
        ])
</div>
@endsection
    
