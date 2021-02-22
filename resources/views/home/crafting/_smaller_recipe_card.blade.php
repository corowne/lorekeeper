<div class="col-md-3 px-1 mb-2">
    <div class="card alert-secondary rounded-0 py-0 col-form-label" data-id="{{ $recipe->id }}" data-name="{{ $recipe->name }}">
        <div class="p-2 row">
            <div class="col">
                @if(isset($recipe->image_url))
                    <img src="{{ $recipe->imageUrl }}" class="recipe-image mr-2" style="max-height:15px; width:auto;">
                @endif
                <h4 class="mb-0 mt-0 d-inline col-form-label">{!! $recipe->displayName !!}</h4>
            </div>
            <div class="col-auto mx-2 text-right"><a class="btn btn-secondary btn-sm ml-2 btn-craft w-100" style="line-height:1;" href="">Craft</a></div>
        </div>
    </div>
</div>
