{{ $reward['quantity'] }} @if(isset($reward['asset']->image_url))<img class="small-icon" src="{{ $reward['asset']->image_url }}">@endif<span>{!! $reward['asset']->displayName !!}</span>
