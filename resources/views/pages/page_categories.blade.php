@extends('world.layout')

@section('title') World Lore @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'World Lore' => '/world/lore']) !!}

<h1>Lore Pages</h1>

<div class="row justify-content-center">
    @foreach($categories as $category)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-transparent text-center pb-0">
                    @if($category->categoryImageUrl)
                        <div class="world-entry-image"><a href="{{ $category->categoryImageUrl }}" data-lightbox="entry" data-title="{{ $category->name }}">
                        <img class="img-fluid" src="{{ $category->categoryImageUrl }}" class="world-entry-image" /></a></div>
                    @endif
                    <h5 class="card-title">{!! $category->name !!}</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center"> 
                    <p class=card-text>{!! $category->description !!}</p>
                    </li>            
                @foreach($pages as $key=>$page)
                @if($page->page_category_id == $category->id)
                    <li class="list-group-item">
                    <p class=card-text>
                    @if($page->is_visible)
                    <a href='{!! $page->url !!}'>{!! $page->title !!}</a>
                    @else
                    <span class="text-muted">{!! $page->title !!}</span>
                    @endif 
                    </p>
                    </li>
                @endif
                @endforeach
                </ul>
            </div>
        </div>
    @endforeach
</div>




@endsection
