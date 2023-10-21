<div class="col-md-3 col-6 mb-3 text-center ">
    <div class="shop-image" style="position: relative;">
    <a href="{{ $area->url }}"><img src="{{ $area->imageUrl }}" style="max-width:250px;filter: blur(20px);" alt="{{ $area->name }}" /></a>
    <a href="{{ $area->url }}"><img src="{{ $area->imageUrl }}" style="position: absolute;max-width:250px;    top:0;
    left:0;
    width: 100%;
    height:100%;" alt="{{ $area->name }}" /></a>
    </div>
    <div class="shop-name mt-1">
        <a href="{{ $area->url }}" class="h5 mb-0">
            {{ $area->name }}
        </a>
    </div>
</div>
