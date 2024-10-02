<div class="row">
      @foreach (\App\Models\EventIcon\EventIcon::where('is_visible', 1)->orderBy('sort', 'DESC')->get() as $eventIcon)
              <div class="eventIcon-item active col"><a href="{{ $eventIcon->link }}"><img class="" src="{{ $eventIcon->imageURL }}" alt="{{ $eventIcon->alt_text }}"></a></div>
      @endforeach
</div>

