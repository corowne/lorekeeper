
      @foreach (\App\Models\EventIcon\EventIcon::where('is_visible', 0)->orderBy('id')->take(1)->get() as $eventIcon)
              <div class="eventicon-item active"><a href="{{ $eventIcon->link }}"><img class="" src="{{ $eventIcon->imageURL }}" alt="{{ $eventIcon->alt_text }}"></a></div>
      @endforeach

