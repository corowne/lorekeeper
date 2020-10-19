@extends('admin.layout')

@section('admin-title') Page Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Page Sections' => 'admin/page-sections', ($section->id ? 'Edit' : 'Create').' Section' => $section->id ? 'admin/page-sections/edit/'.$section->id : 'admin/page-sections/create']) !!}


<h1>{{ $section->id ? 'Edit' : 'Create' }} Section
    @if($section->id)
        <a href="#" class="btn btn-danger float-right delete-section-button">Delete Section</a>
    @endif
</h1>

{!! Form::open(['url' => $section->id ? 'admin/page-sections/edit/'.$section->id : 'admin/page-sections/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $section->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Key') !!}
            {!! Form::text('key', $section->key, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<h3>Contents</h3>
<p>Each category can only have one section. If you assign a category here, it will be removed from any other sections.</p>

<div class="form-group">
    {!! Form::label('categories[]', 'Categories') !!}
    {!! Form::select('categories[]', $categories, $section->categories, ['id' => 'categoryList', 'class' => 'form-control', 'multiple']) !!}
</div>

<div class="text-right">
    {!! Form::submit($section->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
    $( document ).ready(function() {    
        $('.delete-section-button').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/page-sections/delete') }}/{{ $section->id }}", 'Delete Sub Masterlist');
        });
    });

    $(document).ready(function() {
        $('#categoryList').selectize({
        });
        $('.default.item-select').selectize();
        $('#add-item').on('click', function(e) {
            e.preventDefault();
            addItemRow();
        });
        $('.remove-item').on('click', function(e) {
            e.preventDefault();
            removeItemRow($(this));
        })
        function addItemRow() {
            var $rows = $("#itemList > div")
            if($rows.length === 1) {
                $rows.find('.remove-item').removeClass('disabled')
            }
            var $clone = $('.item-row').clone();
            $('#itemList').append($clone);
            $clone.removeClass('hide item-row');
            $clone.addClass('d-flex');
            $clone.find('.remove-item').on('click', function(e) {
                e.preventDefault();
                removeItemRow($(this));
            })
            $clone.find('.item-select').selectize();
        }
        function removeItemRow($trigger) {
            $trigger.parent().remove();
            var $rows = $("#itemList > div")
            if($rows.length === 1) {
                $rows.find('.remove-item').addClass('disabled')
            }
        }
    });
</script>
@endsection