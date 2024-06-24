@extends('admin.layout')

@section('admin-title') Forums @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Forums' => 'admin/forums', ($forum->id ? 'Edit' : 'Create').' Forum' => $forum->id ? 'admin/forums/edit/'.$forum->id : 'admin/forums/create']) !!}

<h1>{{ $forum->id ? 'Edit' : 'Create' }} Forum
    @if($forum->id && !Config::get('lorekeeper.text_forums.'.$forum->key))
        <a href="#" class="btn btn-danger float-right delete-forum-button">Delete Forum</a>
    @endif
</h1>

{!! Form::open(['url' => $forum->id ? 'admin/forums/edit/'.$forum->id : 'admin/forums/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $forum->name, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Sort (Optional)') !!} {!! add_help('Forums are organized by their container (category or board) and then ordered by sort and then by id.') !!}
            {!! Form::number('sort', $forum->sort, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Parent Forum (Optional)') !!} {!! add_help('If you do not pick a parent, this forum will be considered a Category and nobody will be able to make threads in it.') !!}
            {!! Form::select('parent_id', $forums, $forum->parent_id, ['class' => 'form-control', 'placeholder' => 'Select a forum']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Rank Restriction (Optional)') !!} {!! add_help('Only staff and users of this role are able to see and create threads in this forum.') !!}
            {!! Form::select('role_limit', $ranks, $forum->role_limit, ['class' => 'form-control', 'placeholder' => 'Select a role']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    @if($forum->has_image)
        <a href="{!! $forum->imageUrl !!}" data-lightbox="entry" data-title="{!! $forum->name !!}">
            <img src="{!! $forum->imageUrl !!}" class="float-md-left mr-md-5" style="max-with:200px;"/>
        </a>
    @endif
    {!! Form::label('Banner Image (Optional)') !!} {!! add_help('This image is visible at the top of the forum.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">No recommended size.</div>
    @if($forum->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Forum Description') !!} {!! add_help('This should be under 300 characters.') !!}
    {!! Form::textarea('description', $forum->description, ['class' => 'form-control']) !!}
</div>

<div class="row">
    <div class="col-md-4 text-center">
        <div class="form-group">
            {!! Form::checkbox('is_active', 1, $forum->id ? $forum->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, users will not be able to view the forum even if they have the link to it, unless they are staff.') !!}
        </div>
    </div>

    <div class="col-md-4 text-center">
        <div class="form-group">
            {!! Form::checkbox('is_locked', 1, $forum->id ? $forum->is_locked : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_locked', 'Locked', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, users will be able to create new threads and reply to them.') !!}
        </div>
    </div>

    <div class="col-md-4 text-center">
        <div class="form-group">
            {!! Form::checkbox('staff_only', 1, $forum->id ? $forum->staff_only : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('staff_only', 'Staff Only', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned on, only staff members will see this forum.') !!}
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::submit($forum->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.delete-forum-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/forums/delete') }}/{{ $forum->id }}", 'Delete Forum');
    });
});

</script>
@endsection
