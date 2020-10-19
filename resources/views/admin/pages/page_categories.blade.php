@extends('admin.layout')

@section('admin-title') Page Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Page Categories' => 'page-categories']) !!}

<h1>Page Categories</h1>

<p>This is a list of page categories that will be used to sort pages in the encyclopedia. Creating page categories is entirely optional, but recommended if you want to create lore pages for users to browse without linking each one individually.</p> 
<p>Page Categories must be put into Page Sections to be displayed in the encyclopedia. A section is an index page that lists pages by category within it. For example, one can make a Lore section and Guides section, and then create "history" and "deities" categories in Lore and "Prompt Guide" and "Beginners Materials" categories for Guides.
<p>The sorting order reflects the order in which the trait categories will be displayed in the encyclopedia.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/page-categories/create') }}"><i class="fas fa-plus"></i> Create New Page Category</a></div>
@if(!count($categories))
    <p>No page categories found.</p>
@else 
    <table class="table table-sm category-table">
        <tbody id="sortable" class="sortable">
            @foreach($categories as $category)
                <tr class="sort-item" data-id="{{ $category->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        {!! $category->displayName !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/page-categories/edit/'.$category->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/page-categories/sort']) !!}
        {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
        {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>

$( document ).ready(function() {
    $('.handle').on('click', function(e) {
        e.preventDefault();
    });
    $( "#sortable" ).sortable({
        items: '.sort-item',
        handle: ".handle",
        placeholder: "sortable-placeholder",
        stop: function( event, ui ) {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
        },
        create: function() {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
        }
    });
    $( "#sortable" ).disableSelection();
});
</script>
@endsection