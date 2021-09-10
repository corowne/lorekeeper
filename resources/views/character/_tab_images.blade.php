<div class="row">
    @foreach($character->images as $image)
        <div class="col-md-3 col-4">
            <a href="#" class="d-block"><img src="{{ $image->thumbnailUrl }}" class="image-thumb img-thumbnail" alt="Thumbnail for {{ $image->character->fullName }}"/></a>
        </div>
    @endforeach
</div>
