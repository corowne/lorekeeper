{{ $reward->quantity }} @if(isset($reward->reward->image_url))<img class="small-icon" src="{{ $reward->reward->image_url }}">@endif<span>{!! $reward->reward->displayName !!}</span>
