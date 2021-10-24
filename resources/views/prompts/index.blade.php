@extends('prompts.layout')

@section('title') Prompts @endsection

@section('content')
{!! breadcrumbs(['Prompts' => 'prompts']) !!}

<h1>Prompts</h1>
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/inventory.png') }}" alt="Prompts" />
                <h5 class="card-title">Prompts</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ url('prompts/prompt-categories') }}">Prompts Categories</a></li>
                <li class="list-group-item"><a href="{{ url('prompts/prompts') }}">All Prompts</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
