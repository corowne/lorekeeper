{!! Form::open(['url' => 'admin/character/image/'.$image->id.'/credits']) !!}
    <div class="form-group">
        {!! Form::label('Designer(s)') !!}
        <div id="designerList">
            <?php $designerCount = count($image->designers); ?>
            @foreach($image->designers as $count=>$designer)
                <div class="mb-2 d-flex">
                    {!! Form::text('designer_alias['.$designer->id.']', $designer->user->name, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Username']) !!}
                    {!! Form::text('designer_url['.$designer->id.']', $designer->url, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
                    
                    <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer"
                    @if($count != $designerCount - 1)
                        style="visibility: hidden;"
                    @endif
                    >+</a>
                </div>
            @endforeach
            @if(!count($image->designers))
                <div class="mb-2 d-flex">
                    {!! Form::text('designer_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Username']) !!}
                    {!! Form::text('designer_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
                    
                    <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer"
                    >+</a>
                </div>
            @endif
        </div>
        <div class="designer-row hide mb-2">
            {!! Form::text('designer_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Username']) !!}
            {!! Form::text('designer_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
            <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('Artist(s)') !!}
        <div id="artistList">
            <?php $artistCount = count($image->artists); ?>
            @foreach($image->artists as $count=>$artist)
                <div class="mb-2 d-flex">
                    {!! Form::text('artist_alias['.$artist->id.']', $artist->user->name, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Username']) !!}
                    {!! Form::text('artist_url['.$artist->id.']', $artist->url, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
                    <a href="#" class="add-artist btn btn-link" data-toggle="tooltip" title="Add another artist"
                    @if($count != $artistCount - 1)
                        style="visibility: hidden;"
                    @endif
                    >+</a>
                </div>
            @endforeach
            @if(!count($image->artists))
                <div class="mb-2 d-flex">
                    {!! Form::text('artist_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Username']) !!}
                    {!! Form::text('artist_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
                    <a href="#" class="add-artist btn btn-link" data-toggle="tooltip" title="Add another artist"
                    >+</a>
                </div>
            @endif
        </div>
        <div class="artist-row hide mb-2">
            {!! Form::text('artist_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Username']) !!}
            {!! Form::text('artist_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
            <a href="#" class="add-artist btn btn-link mb-2" data-toggle="tooltip" title="Add another artist">+</a>
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $('.add-designer').on('click', function(e) {
            e.preventDefault();
            addDesignerRow($(this));
        });
        function addDesignerRow($trigger) {
            var $clone = $('.designer-row').clone();
            $('#designerList').append($clone);
            $clone.removeClass('hide designer-row');
            $clone.addClass('d-flex');
            $clone.find('.add-designer').on('click', function(e) {
                e.preventDefault();
                addDesignerRow($(this));
            })
            $trigger.css({ visibility: 'hidden' });
        }
        
        $('.add-artist').on('click', function(e) {
            e.preventDefault();
            addArtistRow($(this));
        });
        function addArtistRow($trigger) {
            var $clone = $('.artist-row').clone();
            $('#artistList').append($clone);
            $clone.removeClass('hide artist-row');
            $clone.addClass('d-flex');
            $clone.find('.add-artist').on('click', function(e) {
                e.preventDefault();
                addArtistRow($(this));
            })
            $trigger.css({ visibility: 'hidden' });
        }
    });

</script>