@extends('layouts.app')

@section('title') Forum :: Create Thread in {{ $forum->name }} @endsection

@section('content')
{!! breadcrumbs(['Forum' => 'forum' , $forum->name => 'forum/'.$forum->id, 'Edit Thread' => 'forum/'.$forum->id.'/~'.$thread->id.'/edit' ]) !!}
<h1>Edit {!! $thread->displayName !!}</h1>

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

        <form method="POST" action="{{ route('comments.update',  $thread->getKey()) }}">
            @method('PUT')
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Edit Comment</h5>
                <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="form-group">
                        {!! Form::label('title', 'Title') !!} {!! add_help('Enter a title relevant to your thread.') !!}
                        {!! Form::text('title', $thread->title, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('message', 'Update your message here:') !!}
                        {!! Form::textarea('message', $thread->comment, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <small class="form-text text-muted mb-2">Thread starter posts use HTML.</small>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-outline-success text-uppercase">Update</button>
            </div>
        </form>

    </div>
</div>



@endsection
