@extends('admin.layout')

@section('admin-title') Item Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Item Categories' => 'admin/data/item-categories', ($category->id ? 'Edit' : 'Create').' Category' => $category->id ? 'admin/data/item-categories/edit/'.$category->id : 'admin/data/item-categories/create']) !!}

<h1>{{ $category->id ? 'Edit' : 'Create' }} Category
    @if($category->id)
        <a href="#" class="btn btn-danger float-right delete-category-button">Delete Category</a>
    @endif
</h1>

{!! Form::open(['url' => $category->id ? 'admin/data/item-categories/edit/'.$category->id : 'admin/data/item-categories/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $category->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 200px x 200px</div>
    @if($category->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $category->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="card mb-3" id="characterOptions">
    <div class="card-body">
        <div class="mb-2">
            <div class="form-group">
                {!! Form::checkbox('is_character_owned', 1, $category->is_character_owned, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Allow', 'data-off' => 'Disallow']) !!}
                {!! Form::label('is_character_owned', 'Can Be Owned by Characters', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow items in this category to be owned by characters.') !!}
            </div>
            <div class="form-group">
                {!! Form::label('character_limit', 'Character Hold Limit') !!} {!! add_help('This is the maximum amount of items from this category a character can possess. Set to 0 to allow infinite.') !!}
                {!! Form::text('character_limit', $category ? $category->character_limit : 0, ['class' => 'form-control stock-field', 'data-name' => 'character_limit']) !!}
            </div>
            <div class="form-group">
                {!! Form::checkbox('can_name', 1, $category->can_name, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Allow', 'data-off' => 'Disallow']) !!}
                {!! Form::label('can_name', 'Can be Named', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will set items in this category to be able to be named when in character inventories-- for instance, for pets. Works best in conjunction with a hold limit on the category.') !!}
            </div>
        </div>
    </div>
</div>

@if(Config::get('lorekeeper.settings.donation_shop.item_donations') == 1 || Config::get('lorekeeper.settings.donation_shop.item_donations') == 3)
    <div class="form-group">
        {!! Form::checkbox('can_donate', 1, $category->can_donate, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Allow', 'data-off' => 'Disallow']) !!}
        {!! Form::label('is_character_owned', 'Can Be Donated', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow users to donate items in this category to the Donation Shop.') !!}
    </div>
@endif

<div class="text-right">
    {!! Form::submit($category->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($category->id)
    <h3>Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._item_category_entry', ['imageUrl' => $category->categoryImageUrl, 'name' => $category->displayName, 'description' => $category->parsed_description, 'category'=>$category])
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.delete-category-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/item-categories/delete') }}/{{ $category->id }}", 'Delete Category');
    });
});

</script>
@endsection
