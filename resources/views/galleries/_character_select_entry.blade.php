<div class="submission-character mb-3">
    <div class="row">
        <div class="col-md-4">
            <div>
                <div class="character-image-blank hide">Enter character code.</div>
                <div class="character-image-loaded">
                    @include('galleries._character', ['character' => $character->character])
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <a href="#" class="float-right fas fa-close"></a>
                <div class="form-group">
                    {!! Form::text('slug[]', $character->character->slug, ['class' => 'form-control character-code']) !!}
                </div>
        </div>
        <div class="col-md-1 text-right">
            <a href="#" class="remove-character text-muted"><i class="fas fa-times"></i></a>
        </div>
    </div>
</div>