@extends('home.layout')

@section('home-title') Crafting @endsection

@section('home-content')
{!! breadcrumbs(['Crafting' => 'crafting']) !!}

<h1>
    Crafting
</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            {{--
            <div class="form-group ml-3 mb-3">
                {!! Form::select('prompt_category_id', $categories, Request::get('name'), ['class' => 'form-control']) !!}
            </div>
            --}}
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'alpha'          => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                    {{--
                    'category'       => 'Sort by Category',
                    'newest'         => 'Newest First',
                    'oldest'         => 'Oldest First',--}}
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

{!! $recipes->render() !!}
@foreach($recipes as $recipe)
    @include('home.crafting._recipe_card', ['recipe' => $recipe])
@endforeach
{!! $recipes->render() !!}


@endsection

@section('scripts')
<script>
$( document ).ready(function() {
    $('.btn-craft').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).parent().parent();
        loadModal("{{ url('crafting/craft') }}/" + $parent.data('id'), $parent.data('name'));
    });
});
</script>
@endsection