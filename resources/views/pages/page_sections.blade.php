@extends('world.layout')

@section('title') World Info  @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', $section->name => '/world/info'.$section->key ]) !!}

<h1>{{ $section->name }}</h1>

<div class="row justify-content-center">
    @foreach($section->categories as $category)
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
                @foreach($category->pages as $page)
                    <li class="list-group-item">
                    <p class=card-text>
                    @if($page->is_visible)
                    <a href='{!! $page->url !!}'>{!! $page->title !!}</a>
                    @else
                    <span class="text-muted">{!! $page->title !!}</span>
                    @endif 
                    </p>
                    </li>
                @endforeach
                </ul>
            </div>
        </div>
    @endforeach
</div>

@endsection
