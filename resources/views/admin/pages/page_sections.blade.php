@extends('admin.layout')

@section('admin-title') Page Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Page Sections' => 'page-sections']) !!}

<h1>Page Sections</h1>

<p>A section is an index page that will appear in the encyclopedia containing pages by category.</p> 

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/page-sections/create') }}"><i class="fas fa-plus"></i> Create New Section</a></div>

@if(!count($sections))
    <p>No sections found.</p>
@else 
    <table class="table table-sm category-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Key</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="sortable" class="sortable">
            @foreach($sections as $section)
                <tr class="sort-item" data-id="{{ $section->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        {{ $section->name }}
                    </td>
                    <td>{!! $section->key !!}</td>
                    <td class="text-right">
                        <a href="{{ url('admin/page-sections/edit/'.$section->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/page-sections/sort']) !!}
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