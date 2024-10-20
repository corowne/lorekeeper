    <div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addIngredient">Add Ingredient</a>
</div>
<table class="table table-sm" id="ingredientTable">
    <thead>
        <tr>
            <th width="35%">Ingredient Type</th>
            <th width="35%">Ingredient</th>
            <th width="20%">Quantity</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="ingredientTableBody">
        @if($ingredients)
            @php $row_counter = 0; @endphp
            @foreach($ingredients as $ingredient)
                <tr class="ingredient-row" data-row="{{ $row_counter }}">
                    <td>{!! Form::select('ingredient_type['.$row_counter.']', ['Item' => 'Item', 'MultiItem' => 'Multi Item', 'Category' => 'Category', 'MultiCategory' => 'Multi Category', 'Currency' => 'Currency'], $ingredient->ingredient_type, ['class' => 'form-control ingredient-type', 'placeholder' => 'Select Ingredient Type']) !!}</td>
                    <td class="ingredient-row-select">
                        @switch($ingredient->ingredient_type)
                            @case('Item')
                                {!! Form::select('ingredient_data['.$row_counter.'][]', $items, $ingredient->data[0], ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                                @break
                            @case('MultiItem')
                                <div class="multi-item-select-group">
                                    <div class="multi-item-list">
                                        @foreach($ingredient->data as $entry)
                                            <div class="mb-2">
                                                {!! Form::select('ingredient_data['.$row_counter.'][]', $items, $entry, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                                                <div class="text-right text-uppercase" style="margin-top: -0.5em;"><a href="#" class="remove-multi-entry-button text-danger {{ count($ingredient->data) > 1 ? '' : 'hide' }}">Remove Item</a></div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <a href="#" class="btn btn-primary add-multi-item-button mb-2">Add Item</a>
                                </div>
                                @break
                            @case('Category')
                                {!! Form::select('ingredient_data['.$row_counter.'][]', $categories, $ingredient->data[0], ['class' => 'form-control category-select selectize', 'placeholder' => 'Select Category']) !!}
                                @break
                            @case('MultiCategory')
                                <div class="multi-category-select-group">
                                    <div class="multi-category-list">
                                        @foreach($ingredient->data as $entry)
                                            <div class="mb-2">
                                                {!! Form::select('ingredient_data['.$row_counter.'][]', $categories, $entry, ['class' => 'form-control multi-category-select selectize', 'placeholder' => 'Select Category']) !!}
                                                <div class="text-right text-uppercase" style="margin-top: -0.5em;"><a href="#" class="remove-multi-entry-button text-danger {{ count($ingredient->data) > 1 ? '' : 'hide' }}">Remove Category</a></div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <a href="#" class="btn btn-primary add-multi-category-button mb-2">Add Category</a>
                                </div>
                                @break
                            @case('Currency')
                                {!! Form::select('ingredient_data['.$row_counter.'][]', $currencies, $ingredient->data[0], ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                                @break
                        @endswitch
                    </td>
                    <td>{!! Form::text('ingredient_quantity['.$row_counter.']', $ingredient->quantity, ['class' => 'form-control ingredient_quantity']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-ingredient-button">Remove</a></td>
                </tr>
                @php $row_counter++; @endphp
            @endforeach
        @endif
    </tbody>
</table>