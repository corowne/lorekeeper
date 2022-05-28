@extends('prompts.layout')

@section('title') All Prompts @endsection

@section('content')
    {!! breadcrumbs(['Prompts' => 'prompts', 'All Prompts' => 'prompts/prompts']) !!}
    @include('prompts._prompt_entry', ['prompt' => $prompt, 'isPage' => true])
@endsection
