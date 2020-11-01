<div>
    {!! Form::open(['method' => 'GET']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group mr-3 mb-3">
                {!! Form::label('name', 'Character Name/Code: ', ['class' => 'mr-2']) !!}
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group mb-3">
                {!! Form::select('rarity_id', $rarities, Request::get('rarity_id'), ['class' => 'form-control mr-3']) !!}
            </div>
            <div class="form-group mb-3">
                {!! Form::select('species_id', $specieses, Request::get('species_id'), ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="text-right mb-3"><a href="#advancedSearch" class="btn btn-sm btn-outline-info" data-toggle="collapse">Show Advanced Search Options <i class="fas fa-caret-down"></i></a></div>
        <div class="card bg-light mb-3 collapse" id="advancedSearch">
            <div class="card-body masterlist-advanced-search">
                @if(!$isMyo)
                    <div class="masterlist-search-field">
                        {!! Form::label('character_category_id', 'Category: ') !!}
                        {!! Form::select('character_category_id', $categories, Request::get('character_category_id'), ['class' => 'form-control']) !!}
                    </div>
                    <div class="masterlist-search-field">
                        {!! Form::label('subtype_id', 'Species Subtype: ') !!}
                        {!! Form::select('subtype_id', $subtypes, Request::get('subtype_id'), ['class' => 'form-control']) !!}
                    </div>
                @endif
                <div class="masterlist-search-field">
                    {!! Form::label('username', 'Owner Name: ') !!}
                    {!! Form::text('username', Request::get('username'), ['class' => 'form-control']) !!}
                </div>
                <div class="masterlist-search-field">
                    {!! Form::label('artist', 'Artist Alias: ') !!}
                    {!! Form::text('artist', Request::get('artist'), ['class' => 'form-control']) !!}
                </div>
                <div class="masterlist-search-field">
                    {!! Form::label('designer', 'Designer Alias: ') !!}
                    {!! Form::text('designer', Request::get('designer'), ['class' => 'form-control']) !!}
                </div>
                <hr />
                <div class="masterlist-search-field">
                    {!! Form::label('sale_value_min', 'Resale Minimum ($): ') !!}
                    {!! Form::text('sale_value_min', Request::get('sale_value_min'), ['class' => 'form-control']) !!}
                </div>
                <div class="masterlist-search-field">
                    {!! Form::label('sale_value_max', 'Resale Maximum ($): ') !!}
                    {!! Form::text('sale_value_max', Request::get('sale_value_max'), ['class' => 'form-control']) !!}
                </div>
                @if(!$isMyo)
                    <div class="masterlist-search-field">
                        {!! Form::label('is_gift_art_allowed', 'Gift Art Status: ') !!}
                        {!! Form::select('is_gift_art_allowed', [0 => 'Any', 2 => 'Ask First', 1 => 'Yes', 3 => 'Yes OR Ask First'], Request::get('is_gift_art_allowed'), ['class' => 'form-control']) !!}
                    </div>
                    <div class="masterlist-search-field">
                        {!! Form::label('is_gift_writing_allowed', 'Gift Writing Status: ') !!}
                        {!! Form::select('is_gift_writing_allowed', [0 => 'Any', 2 => 'Ask First', 1 => 'Yes', 3 => 'Yes OR Ask First'], Request::get('is_gift_writing_allowed'), ['class' => 'form-control']) !!}
                    </div>
                @endif
                <br />
                {{-- Setting the width and height on the toggles as they don't seem to calculate correctly if the div is collapsed. --}}
                <div class="masterlist-search-field">
                    {!! Form::checkbox('is_trading', 1, Request::get('is_trading'), ['class' => 'form-check-input',  'data-toggle' => 'toggle', 'data-on' => 'Open For Trade', 'data-off' => 'Any Trading Status', 'data-width' => '200', 'data-height' => '46']) !!}
                </div>
                <div class="masterlist-search-field">
                    {!! Form::checkbox('is_sellable', 1, Request::get('is_sellable'), ['class' => 'form-check-input',  'data-toggle' => 'toggle', 'data-on' => 'Can Be Sold', 'data-off' => 'Any Sellable Status', 'data-width' => '204', 'data-height' => '46']) !!}
                </div>
                <div class="masterlist-search-field">
                    {!! Form::checkbox('is_tradeable', 1, Request::get('is_tradeable'), ['class' => 'form-check-input',  'data-toggle' => 'toggle', 'data-on' => 'Can Be Traded', 'data-off' => 'Any Tradeable Status', 'data-width' => '220', 'data-height' => '46']) !!}
                </div>
                <div class="masterlist-search-field">
                    {!! Form::checkbox('is_giftable', 1, Request::get('is_giftable'), ['class' => 'form-check-input',  'data-toggle' => 'toggle', 'data-on' => 'Can Be Gifted', 'data-off' => 'Any Giftable Status', 'data-width' => '202', 'data-height' => '46']) !!}
                </div>
                <hr />
                    <a href="#" class="float-right btn btn-sm btn-outline-primary add-feature-button">Add Trait</a>
                    {!! Form::label('Has Traits: ') !!} {!! add_help('This will narrow the search to characters that have ALL of the selected traits at the same time.') !!}
                    <div id="featureBody" class="row">
                        @if(Request::get('feature_id'))
                            @foreach(Request::get('feature_id') as $featureId)
                                <div class="feature-block col-md-3 col-sm-4 col-6 mt-3">
                                    <div class="card">
                                        <div class="card-body d-flex">
                                            {!! Form::select('feature_id[]', $features, $featureId, ['class' => 'form-control feature-select selectize', 'placeholder' => 'Select Trait']) !!}
                                            <a href="#" class="btn feature-remove ml-2"><i class="fas fa-times"></i></a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                <hr />
                <div class="masterlist-search-field">
                    {!! Form::checkbox('search_images', 1, Request::get('search_images'), ['class' => 'form-check-input mr-3',  'data-toggle' => 'toggle']) !!}
                    <span class="ml-2">Include all character images in search {!! add_help('Each character can have multiple images for each updated version of the character, which captures the traits on that character at that point in time. By default the search will only search on the most up-to-date image, but this option will retrieve characters that match the criteria on older images - you may get results that are outdated.') !!}</span>
                </div>
                
            </div>

        </div>
        <div class="form-inline justify-content-end mb-3">
            <div class="form-group mr-3">
                {!! Form::label('sort', 'Sort: ', ['class' => 'mr-2']) !!}
                {!! Form::select('sort', ['number_desc' => 'Number Descending', 'number_asc' => 'Number Ascending', 'id_desc' => 'Newest First', 'id_asc' => 'Oldest First', 'sale_value_desc' => 'Highest Sale Value', 'sale_value_asc' => 'Lowest Sale Value'], Request::get('sort'), ['class' => 'form-control']) !!}
            </div>
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>
<div class="hide" id="featureContent">
    <div class="feature-block col-md-3 col-sm-4 col-6 mt-3">
        <div class="card">
            <div class="card-body d-flex">
                {!! Form::select('feature_id[]', $features, null, ['class' => 'form-control feature-select selectize', 'placeholder' => 'Select Trait']) !!}
                <a href="#" class="btn feature-remove ml-2"><i class="fas fa-times"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="text-right mb-3">
    <div class="btn-group">
        <button type="button" class="btn btn-secondary active grid-view-button" data-toggle="tooltip" title="Grid View" alt="Grid View"><i class="fas fa-th"></i></button>
        <button type="button" class="btn btn-secondary list-view-button" data-toggle="tooltip" title="List View" alt="List View"><i class="fas fa-bars"></i></button>
    </div>
</div>

{!! $characters->render() !!}
<div id="gridView" class="hide">
    @foreach($characters->chunk(4) as $chunk)
        <div class="row">
            @foreach($chunk as $character)
            <div class="col-md-3 col-6 text-center">
                <div>
                    <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                </div>
                <div class="mt-1">
                    <a href="{{ $character->url }}" class="h5 mb-0">@if(!$character->is_visible) <i class="fas fa-eye-slash"></i> @endif {{ $character->fullName }}</a>
                </div>
                <div class="small">
                    {!! $character->image->species_id ? $character->image->species->displayName : 'No Species' !!} ・ {!! $character->image->rarity_id ? $character->image->rarity->displayName : 'No Rarity' !!} ・ {!! $character->displayOwner !!}
                </div>
            </div>
            @endforeach
        </div>
    @endforeach
</div>
<div id="listView" class="hide">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Owner</th>
                <th>Name</th>
                <th>Rarity</th>
                <th>Species</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($characters as $character)
                <tr>
                    <td>{!! $character->displayOwner !!}</td>
                    <td>@if(!$character->is_visible) <i class="fas fa-eye-slash"></i> @endif {!! $character->displayName !!}</td>
                    <td>{!! $character->image->rarity_id ? $character->image->rarity->displayName : 'None' !!}</td>
                    <td>{!! $character->image->species_id ? $character->image->species->displayName : 'None' !!}</td>
                    <td>{!! format_date($character->created_at) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{!! $characters->render() !!}

<div class="text-center mt-4 small text-muted">{{ $characters->total() }} result{{ $characters->total() == 1 ? '' : 's' }} found.</div>