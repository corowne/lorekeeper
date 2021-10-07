<h3>Your Characters <a class="small characters-collapse-toggle collapse-toggle" href="#userCharacters" data-toggle="collapse">Show</a></h3>
<div class="card mb-3 collapse show" id="userCharacters">
    <div class="card-body">
        <div class="text-right mb-3">
            <div class="d-inline-block">
                {!! Form::label('character_category_id', 'Filter:', ['class' => 'mr-2']) !!}
                <select class="form-control d-inline-block w-auto" id="userCharacterCategory">
                    <option value="all">All Categories</option>
                    <option value="selected">Selected Characters</option>
                    <option disabled>&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-inline-block">
                {!! Form::label('character_category_id', 'Action:', ['class' => 'ml-2 mr-2']) !!}
                <a href="#" class="btn btn-primary characters-select-all">Select All Visible</a>
                <a href="#" class="btn btn-primary characters-clear-selection">Clear Visible Selection</a>
            </div>
        </div>
        <div class="user-characters">
            <div class="row">
                @foreach($characters as $character)
                    <div class="col-lg-2 col-sm-3 col-6 mb-3 user-character category-all category-{{ $character->character_category_id ? : 0 }} {{ isset($selected) && in_array($character->id, $selected) ? 'category-selected' : '' }} {{ (isset($selected) && in_array($character->id, $selected)) || $character->isAvailable ? '' : 'select-disabled' }}" data-id="{{ $character->id }}">
                        <div class="text-center character-item {{ (isset($selected) && in_array($character->id, $selected)) || $character->isAvailable ? '' : 'disabled' }}" @if(!(isset($selected) && in_array($character->id, $selected)) && !$character->isAvailable) data-toggle="tooltip" title="{{ $character->trade_id ? 'This character is in a trade.' : 'This character has an active design update.' }}" @endif>
                            <div class="mb-1">
                                <a class="character-stack"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" alt="Thumbnail for {{ $character->fullName }}" /></a>
                            </div>
                            <div>
                                <a class="character-stack character-stack-name">{{ $character->slug }}</a>
                                {!! Form::checkbox((isset($fieldName) && $fieldName ? $fieldName : 'character_id[]'), $character->id, isset($selected) && in_array($character->id, $selected) ? true : false, ['class' => 'character-checkbox hide']) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
