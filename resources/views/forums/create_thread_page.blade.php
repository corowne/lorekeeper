@extends('layouts.app')

@section('title') Forum :: Create Thread in {{ $forum->name }} @endsection

@section('content')
{!! breadcrumbs(['Forum' => 'forum' , $forum->name => 'forum/'.$forum->id, 'Create New Thread' => 'forum/'.$forum->id.'/new' ]) !!}
<h1>Create Thread in {{ $forum->displayName }}</h1>

ssssssssssssssssssss

@endsection
