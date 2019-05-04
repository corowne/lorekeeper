@extends('layouts.app')

@section('title') Site News @endsection

@section('content')
{!! breadcrumbs(['Site News' => 'news']) !!}
<h1>Site News</h1>
{!! $newses->render() !!}
@foreach($newses as $news)
    @include('news._news', ['news' => $news])
@endforeach
{!! $newses->render() !!}
@endsection
