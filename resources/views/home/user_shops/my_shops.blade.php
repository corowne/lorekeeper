@extends('home.layout')

@section('home-title') Shops @endsection

@section('home-content')
{!! breadcrumbs(['My Shops' => 'usershops']) !!}

<h1>Shops</h1>

<p>Here is a list of your user-owned shops. </p> 
<p>The sorting order reflects the order in which the shops will be listed on the shop index.</p>
@if(Settings::get('user_shop_limit') > 0)
<p> You may make a maximum of <b>{{Settings::get('user_shop_limit')}}</b> shops.</p>
@endif

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('usershops/create') }}"><i class="fas fa-plus"></i> Create New Shop</a></div>
@if(!count($shops))
    <p>No item shops found.</p>
@else 
    <table class="table table-sm shop-table">
        <tbody id="sortable" class="sortable">
            @foreach($shops as $shop)
                <tr class="sort-item" data-id="{{ $shop->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        {!! $shop->displayName !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('usershops/edit/'.$shop->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'usershops/sort']) !!}
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