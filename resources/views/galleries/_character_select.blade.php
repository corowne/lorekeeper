<div id="characterComponents" class="hide">
    <div class="submission-character mb-3">
        <div class="row">
            <div class="col-md-4">
                <div>
                    <div class="character-image-blank">Enter character code.</div>
                    <div class="character-image-loaded hide"></div>
                </div>
            </div>
            <div class="col-md-7">
                <a href="#" class="float-right fas fa-close"></a>
                    <div class="form-group">
                        {!! Form::text('slug[]', null, ['class' => 'form-control character-code', 'placeholder' => 'Character Code (EX-001, for example)']) !!}
                    </div>
            </div>
            <div class="col-md-1 text-right">
                <a href="#" class="remove-character text-muted"><i class="fas fa-times"></i></a>
            </div>
        </div>
    </div>
</div>