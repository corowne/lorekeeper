@switch($ingredient->ingredient_type)
    @case('Item')
        {{ $ingredient->quantity }} @if(isset($ingredient->ingredient->image_url))<img class="small-icon" src="{{ $ingredient->ingredient->image_url }}">@endif<span>{!! $ingredient->ingredient->displayName !!}</span>
        @break
    @case('MultiItem')
        <strong>Any mix of {{ $ingredient->quantity }} item{{ $ingredient->quantity == 1 ? '' : 's'}} from the following:</strong>
        <p class="mb-0">
            @foreach($ingredient->ingredient as $key => $ing)
                @if(isset($ing->image_url))<img class="small-icon" src="{{ $ing->image_url }}">@endif <strong>{!! $ing->displayName !!}</strong>{{ ($key < $ingredient->ingredient->count()-1) && ($ingredient->ingredient->count() > 2) ? ', ' : '' }}{{ ($key == $ingredient->ingredient->count()-2) && ($ingredient->ingredient->count() > 1) ? ' or ' : '' }}
            @endforeach
        </p>
        @break
    @case('Category')
        {{ $ingredient->quantity }} item{{ $ingredient->quantity == 1 ? '' : 's'}} from the 
        @if(isset($ingredient->ingredient->image_url))<img class="small-icon" src="{{ $ingredient->ingredient->image_url }}">@endif{!! $ingredient->ingredient->displayName !!} 
        category
        @break
    @case('MultiCategory')
        <!-- This doesn't work yet! -->
        <strong>Any mix of {{ $ingredient->quantity }} item{{ $ingredient->quantity == 1 ? '' : 's'}} from the following categories:</strong>
        @foreach($ingredient->ingredient as $ing)
            <div>- @if(isset($ing->image_url))<img class="small-icon" src="{{ $ing->image_url }}">@endif<span>{!! $ing->displayName !!}</span></div>
        @endforeach
        @break
    @case('Currency')
        {{ $ingredient->quantity }} {!! $ingredient->ingredient->display_name !!}
        @break
@endswitch