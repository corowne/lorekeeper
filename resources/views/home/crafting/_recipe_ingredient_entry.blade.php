@switch($ingredient->ingredient_type)
    @case('Item')
        {{ $ingredient->quantity }} @if(isset($ingredient->ingredient->image_url))<img class="small-icon" src="{{ $ingredient->ingredient->image_url }}">@endif<span>{!! $ingredient->ingredient->displayName !!}</span>
        @break
    @case('MultiItem')
        <p>Any mix of {{ $ingredient->quantity }} item(s) from the following:</p>
        @foreach($ingredient->ingredient as $ing)
            <div>- @if(isset($ing->image_url))<img class="small-icon" src="{{ $ing->image_url }}">@endif<span>{!! $ing->displayName !!}</span></div>
        @endforeach
        @break
    @case('Category')
        {{ $ingredient->quantity }} item(s) from the 
        @if(isset($ingredient->ingredient->image_url))<img class="small-icon" src="{{ $ingredient->ingredient->image_url }}">@endif{!! $ingredient->ingredient->displayName !!} 
        category
        @break
    @case('MultiCategory')
        Any mix of {{ $ingredient->quantity }} item(s) from the following categories:</p>
        @foreach($ingredient->ingredient as $ing)
            <div>- @if(isset($ing->image_url))<img class="small-icon" src="{{ $ing->image_url }}">@endif<span>{!! $ing->displayName !!}</div>
        @endforeach
    @case('Currency')
        {{ $ingredient->quantity }} @if(isset($ingredient->image_url))<img class="small-icon" src="{{ $ingredient->ingredient->image_url }}">@endif<span>{!! $ingredient->ingredient->displayName !!}</span>
        @break
    @break
@endswitch