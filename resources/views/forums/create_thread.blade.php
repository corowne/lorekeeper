@extends('layouts.app')

@section('title') Forum :: Create Thread in {{ $forum->name }} @endsection

@section('content')
{!! breadcrumbs(['Forum' => 'forum' , $forum->name => 'forum/'.$forum->id, 'Create New Thread' => 'forum/'.$forum->id.'/new' ]) !!}
<h1>Create Thread in {!! $forum->displayName !!}</h1>

<div class="card">
    <div class="card-body">
        @if($errors->has('commentable_type'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('commentable_type') }}
            </div>
        @endif
        @if($errors->has('commentable_id'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('commentable_id') }}
            </div>
        @endif
        <form method="POST" action="{{ route('comments.store') }}">
            @csrf
            @honeypot
            <input type="hidden" name="commentable_type" value="\App\Models\Forum" />
            <input type="hidden" name="commentable_id" value="{{ $forum->id }}" />
            <input type="hidden" name="type" value="{{ isset($type) ? $type : null }}" />

            <div class="form-group">
                {!! Form::label('title', 'Title') !!} {!! add_help('Enter a title relevant to your thread.') !!}
                {!! Form::text('title',  Request::get('title'), ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('message', 'Message') !!}
                {!! Form::textarea('message',  Request::get('message'), ['class' => 'form-control ', 'required']) !!}
            </div>
            <small class="form-text text-muted mb-2">Thread starter posts use HTML.</small>
            <button type="submit" class="btn btn-sm btn-outline-success text-uppercase">Submit</button>
        </form>
    </div>
</div>



@endsection
