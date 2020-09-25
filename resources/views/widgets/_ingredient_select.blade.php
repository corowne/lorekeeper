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
                <tr class="ingredient-row row_num_{{ $row_counter }}">
                    <td>{!! Form::select('ingredient_type[]', ['Item' => 'Item', 'MultiItem' => 'Multi Item', 'Category' => 'Category', 'Multi Category' => 'MultiCategory', 'Currency' => 'Currency'], $ingredient->ingredient_type, ['class' => 'form-control ingredient-type', 'placeholder' => 'Select Ingredient Type']) !!}</td>
                    <td class="ingredient-row-select">
                        @if($ingredient->ingredient_type == 'Item')
                            {!! Form::select('ingredient_data[$row_counter][]', $items, $ingredient->ingredient_data, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                        @elseif($ingredient->ingredient_type == 'Currency')
                            {!! Form::select('ingredient_data[$row_counter][]', $currencies, $ingredient->ingredient_data, ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                        @endif
                    </td>
                    <td>{!! Form::text('quantity[]', $ingredient->quantity, ['class' => 'form-control']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-ingredient-button">Remove</a></td>
                </tr>
                @php $row_counter++; @endphp
            @endforeach
        @endif
    </tbody>
</table>