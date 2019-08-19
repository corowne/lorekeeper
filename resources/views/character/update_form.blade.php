@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->is_myo_slot ? 'MYO Approval' : 'Design Update' }} for {{ $character->fullName }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, ($character->is_myo_slot ? 'MYO Approval' : 'Design Update') => $character->url.'/approval']) !!}

@include('character._header', ['character' => $character])

<h3>
    {{ $character->is_myo_slot ? 'MYO Approval' : 'Design Update' }} Request
</h3>
@if(!$queueOpen)
    <div class="alert alert-danger">
        The {{ $character->is_myo_slot ? 'MYO approval' : 'design update' }} queue is currently closed. You cannot submit a new approval request at this time.
    </div>
@else
    <p>This form is for submitting this {{ $character->is_myo_slot ? 'MYO slot' : 'character' }} to the {{ $character->is_myo_slot ? 'MYO approval' : 'design update' }} queue. Keeping in mind the allowed traits/stats for your update request, select the desired traits/stats below. You may also select currency and items to spend on this approval - these will be deducted from your account immediately, but refunded to you in case of a rejection.</p>
    {!! Form::open(['url' => $character->is_myo_slot ? 'myo/'.$character->id.'/approval' : 'character/'.$character->slug.'/approval']) !!}
    <div class="form-group">
        {!! Form::label('comments', 'Comments (Optional)') !!} {!! add_help('Enter a comment that will be added onto your '.($character->is_myo_slot ? 'MYO approval' : 'design update').' request - suggestions would be to include calculations or how you intend to use attached items or currency if applicable. Staff will read this comment while reviewing your request.') !!}
        {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
    </div>

    <h3>Image Upload</h3>

    <div class="form-group">
        {!! Form::label('Image') !!} {!! add_help('This is the image that will be used on the masterlist. Note that the image is not protected in any way, so take precautions to avoid art/design theft.') !!}
        <div>{!! Form::file('image', ['id' => 'mainImage']) !!}</div>
    </div>
    {{--
    <div class="form-group">
        {!! Form::label('url', 'URL') !!} {!! add_help('This is an optional additional image to supplement the above. Note that the image is not protected in any way, so take precautions to avoid art/design theft.') !!}
        <div>{!! Form::text('url', null, ['class' => 'form-control']) !!}</div>
    </div>
    --}}
    <div class="form-group">
        {!! Form::checkbox('use_cropper', 1, 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'id' => 'useCropper']) !!}
        {!! Form::label('use_cropper', 'Use Image Cropper', ['class' => 'form-check-label ml-3']) !!} {!! add_help('A thumbnail is required for the upload (used for the masterlist). You can use the image cropper (crop dimensions can be adjusted in the site code), or upload a custom thumbnail.') !!}
    </div>
    <div class="card mb-3" id="thumbnailCrop">
        <div class="card-body">
            <div id="cropSelect">Select an image to use the thumbnail cropper.</div>
            <img src="#" id="cropper" class="hide" />
            {!! Form::hidden('x0', null, ['id' => 'cropX0']) !!}
            {!! Form::hidden('x1', null, ['id' => 'cropX1']) !!}
            {!! Form::hidden('y0', null, ['id' => 'cropY0']) !!}
            {!! Form::hidden('y1', null, ['id' => 'cropY1']) !!}
        </div>
    </div>
    <div class="card mb-3" id="thumbnailUpload">
        <div class="card-body">
            {!! Form::label('Thumbnail Image') !!} {!! add_help('This image is shown on the masterlist page.') !!}
            <div>{!! Form::file('thumbnail') !!}</div>
            <div class="text-muted">Recommended size: 200px x 200px</div>
        </div>
    </div>
    <p class="alert alert-info">
        This section is for crediting the image creators. The first box is for the designer's deviantART name (if any). If the designer has an account on the site, it will link to their site profile; if not, it will link to their dA page. The second is for a custom URL if they don't use dA. Both are optional - you can fill in the alias and ignore the URL, or vice versa. If you fill in both, it will link to the given URL, but use the alias field as the link name.
    </p>
        <div class="form-group">
            {!! Form::label('Designer(s)') !!}
            <div id="designerList">
                <div class="mb-2 d-flex">
                    {!! Form::text('designer_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Alias']) !!}
                    {!! Form::text('designer_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
                    <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
                </div>
            </div>
            <div class="designer-row hide mb-2">
                {!! Form::text('designer_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer Alias']) !!}
                {!! Form::text('designer_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Designer URL']) !!}
                <a href="#" class="add-designer btn btn-link" data-toggle="tooltip" title="Add another designer">+</a>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('Artist(s)') !!}
            <div id="artistList">
                <div class="mb-2 d-flex">
                    {!! Form::text('artist_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Alias']) !!}
                    {!! Form::text('artist_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
                    <a href="#" class="add-artist btn btn-link" data-toggle="tooltip" title="Add another artist">+</a>
                </div>
            </div>
            <div class="artist-row hide mb-2">
                {!! Form::text('artist_alias[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist Alias']) !!}
                {!! Form::text('artist_url[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Artist URL']) !!}
                <a href="#" class="add-artist btn btn-link mb-2" data-toggle="tooltip" title="Add another artist">+</a>
            </div>
        </div>

        <h3>Add-ons</h3>
        <p>You can select items from your inventory and/or currencies from your bank{{ $character->is_myo_slot ? '' : ' or your character\'s bank' }} to attach to this request. Note that this will consume the items/currency, but will be refunded if the request is rejected. This is entirely optional; please follow any restrictions set by staff regarding restrictions on what you may add to a request.</p>

        <h3>Traits</h3>
        <p>Select the traits for the {{ $character->is_myo_slot ? 'created' : 'updated' }} character. @if($character->is_myo_slot) Some traits may have been restricted for you - you cannot change them. @endif Staff will not be able to modify these traits for you during approval, so if in doubt, please communicate with them beforehand to make sure that your design is acceptable.</p>
        <div class="form-group">
            {!! Form::label('species_id', 'Species') !!}
            @if($character->is_myo_slot && $character->image->species_id) 
                <div class="alert alert-secondary">{!! $character->image->species->displayName !!}</div>
            @else
                {!! Form::select('species_id', $specieses, $character->image->species_id, ['class' => 'form-control', 'id' => 'species']) !!}
            @endif
            
        </div>

        <div class="form-group">
            {!! Form::label('rarity_id', 'Character Rarity') !!}
            @if($character->is_myo_slot && $character->image->rarity_id) 
                <div class="alert alert-secondary">{!! $character->image->rarity->displayName !!}</div>
            @else
                {!! Form::select('rarity_id', $rarities, $character->image->rarity_id, ['class' => 'form-control', 'id' => 'rarity']) !!}
            @endif
        </div>

        <div class="form-group">
            {!! Form::label('Traits') !!} 
            <div id="featureList">
                @if($character->image->features)
                    @foreach($character->image->features as $feature)
                        <div class="mb-2 d-flex">
                            {!! Form::select('feature_id[]', $features, $feature->feature_id, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Select Trait', $character->is_myo_slot ? 'disabled' : '']) !!}
                            {!! Form::text('feature_data[]', $feature->data, ['class' => 'form-control mr-2', 'placeholder' => 'Extra Info (Optional)', $character->is_myo_slot ? 'disabled' : '']) !!}
                            @if(!$character->is_myo_slot)
                                <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
            <div><a href="#" class="btn btn-primary" id="add-feature">Add Trait</a></div>
            <div class="feature-row hide mb-2">
                {!! Form::select('feature_id[]', $features, null, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Select Trait']) !!}
                {!! Form::text('feature_data[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Extra Info (Optional)']) !!}
                <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
@endif

@endsection

@section('scripts')
@include('widgets._image_upload_js')
<script>
    $(document).ready(function(){
    });
</script>
@endsection