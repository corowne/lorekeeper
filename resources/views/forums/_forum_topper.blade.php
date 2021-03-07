@if($forum->has_image)
    <div class="text-center mb-2" style="clear:both;">
        <a href="{!! $forum->imageUrl !!}" data-lightbox="entry" data-title="{!! $forum->name !!}">
            <img src="{!! $forum->imageUrl !!}" class="mw-100"/>
        </a>
    </div>
@endif

@if(isset($forum->description))
    <div class="mb-2"style="clear:both;">
        {!! $forum->parsed_description !!}
    </div>
@endif
