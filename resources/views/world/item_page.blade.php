@extends('world.layout')

@section('title') {{ $item->name }} @endsection

@section('meta-img') {{ $imageUrl }} @endsection

@section('meta-desc')
@if(isset($item->category) && $item->category) <p><strong>Category:</strong> {{ $item->category->name }}</p> @endif
@if(isset($item->rarity) && $item->rarity) :: <p><strong>Rarity:</strong> {{ $item->rarity }}: {{ $item->rarityName }}</p> @endif
 :: {!! substr(str_replace('"','&#39;',$item->description),0,69) !!}
@if(isset($item->uses) && $item->uses) :: <p><strong>Uses:</strong> {!! $item->uses !!}</p> @endif
@endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Items' => 'world/items', $item->name => $item->idUrl]) !!}

<div class="row">
    <div class="col-sm">
    </div>
    <div class="col-lg-6 col-lg-10">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row world-entry">
                    @if($imageUrl)
                        <div class="col-md-3 world-entry-image"><a href="{{ $imageUrl }}" data-lightbox="entry" data-title="{{ $name }}"><img src="{{ $imageUrl }}" class="world-entry-image" alt="{{ $name }}" /></a></div>
                    @endif
                    <div class="{{ $imageUrl ? 'col-md-9' : 'col-12' }}">
                        <h1>{!! $name !!}</h1>
                        <div class="row">
                        @if(isset($item->category) && $item->category)
                            <div class="col-md">
                                <p><strong>Category:</strong> {!! $item->category->name !!}</p>
                            </div>
                        @endif
                        @if(Config::get('lorekeeper.extensions.item_entry_expansion.extra_fields'))
                            @if(isset($item->rarity) && $item->rarity)
                                <div class="col-md">
                                    <p><strong>Rarity:</strong> {!! $item->rarity !!}</p>
                                </div>
                            @endif
                            @if(isset($item->itemArtist) && $item->itemArtist)
                                <div class="col-md">
                                    <p><strong>Artist:</strong> {!! $item->itemArtist !!}</p>
                                </div>
                            @endif
                        @endif
                        @if(isset($item->data['resell']) && $item->data['resell'] && Config::get('lorekeeper.extensions.item_entry_expansion.resale_function'))
                            <div class="col-md">
                                <p><strong>Resale Value:</strong> {!! App\Models\Currency\Currency::find($item->resell->flip()->pop())->display($item->resell->pop()) !!}</p>
                            </div>
                        @endif
                            <div class="col-md-5 col-md">
                                <div class="row">
                                    @foreach($item->tags as $tag)
                                        @if($tag->is_active)
                                        <div class="col">
                                            {!! $tag->displayTag !!}
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="world-entry-text">
                            @if(isset($item->reference) && $item->reference && Config::get('lorekeeper.extensions.item_entry_expansion.extra_fields'))  <p><strong>Reference Link:</strong> <a href="{{ $item->reference }}">{{ $item->reference }}</a></p> @endif
                            {!! $description !!}
                            @if((isset($item->uses) && $item->uses || isset($item->source) && $item->source || $shops->count() || isset($item->data['prompts']) && $item->data['prompts']) && Config::get('lorekeeper.extensions.item_entry_expansion.extra_fields'))

                                @if(isset($item->uses) && $item->uses)  <p><strong>Uses:</strong> {!! $item->uses !!}</p> @endif
                                @if(isset($item->source) && $item->source || $shops->count() || isset($item->data['prompts']) && $item->data['prompts'])
                                <h5>Availability</h5>
                                <div class="row">
                                    @if(isset($item->data['release']) && $item->data['release'])
                                        <div class="col">
                                            <p><strong>Source:</strong></p>
                                            <p>{!! $item->data['release'] !!}</p>
                                        </div>
                                    @endif
                                    @if($shops->count())
                                        <div class="col">
                                            <p><strong>Purchaseable At:</strong></p>
                                                <div class="row">
                                                    @foreach($shops as $shop) <span class="badge" style="font-size:95%; margin:5px;"><a href="{{ $shop->url }}">{{ $shop->name }}</a></span>
                                                    @endforeach
                                                </div>
                                        </div>
                                    @endif
                                    @if(isset($item->data['prompts']) && $item->data['prompts'])
                                        <div class="col">
                                            <p><strong>Drops From:</strong></p>
                                                <div class="row">
                                                    @foreach($item->prompts as $prompt) <span class="badge" style="font-size:95%; background-color: #fefcf6; margin:5px;"><a href="{{ $prompt->url }}">{{ $prompt->name }}</a></span> @endforeach
                                                </div>
                                        </div>
                                    @endif
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm">
    </div>
</div>
@endsection
