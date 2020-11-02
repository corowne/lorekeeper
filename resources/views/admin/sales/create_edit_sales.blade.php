@extends('admin.layout')

@section('admin-title') Sales @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Sales' => 'admin/sales', ($sales->id ? 'Edit' : 'Create').' Post' => $sales->id ? 'admin/sales/edit/'.$sales->id : 'admin/sales/create']) !!}

<h1>{{ $sales->id ? 'Edit' : 'Create' }} Post
    @if($sales->id)
        <a href="#" class="btn btn-danger float-right delete-sales-button">Delete Post</a>
    @endif
</h1>

{!! Form::open(['url' => $sales->id ? 'admin/sales/edit/'.$sales->id : 'admin/sales/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Title') !!}
            {!! Form::text('title', $sales->title, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Post Time (Optional)') !!} {!! add_help('This is the time that the sales post should be posted. Make sure the Is Viewable switch is off.') !!}
            {!! Form::text('post_at', $sales->post_at, ['class' => 'form-control datepicker']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Post Content') !!}
    {!! Form::textarea('text', $sales->text, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="row">
    <div class="col-md">
        <div class="form-group">
            {!! Form::checkbox('is_visible', 1, $sales->id ? $sales->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_visible', 'Is Viewable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, the post will not be visible. If the post time is set, it will automatically become visible at/after the given post time, so make sure the post time is empty if you want it to be completely hidden.') !!}
        </div>
    </div>
    @if($sales->id && $sales->is_visible)
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('bump', 1, null, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('bump', 'Bump Sale', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If toggled on, this will alert users that there is a new sale. Best in conjunction with a clear notification of changes!') !!}
            </div>
        </div>
    @endif
    <div class="col-md">
        <div class="form-group">
            {!! Form::checkbox('is_open', 1, $sales->id ? $sales->is_open : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_open', 'Is Open', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Whether or not the sale is open; used to label the post in the title. This should be on unless the sale is finished; if a time is set for comments to open, the sale will be labeled as \'Preview\' instead.') !!}
        </div>
    </div>
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('comments_open_at', 'Comments Open At (Optional)') !!} {!! add_help('The time at which comments open to members. Staff can post comments before this time.') !!}
            {!! Form::text('comments_open_at', $sales->comments_open_at, ['class' => 'form-control datepicker']) !!}
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::submit($sales->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-sales-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/sales/delete') }}/{{ $sales->id }}", 'Delete Post');
    });

    $( ".datepicker" ).datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: 'HH:mm:ss',
    });
}); 
</script>
@endsection