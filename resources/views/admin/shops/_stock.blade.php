<div class="card mb-3 stock {{ $stock ? '' : 'hide' }}">
    <div class="card-body">
        <div class="text-right mb-3"><a href="#" class="remove-stock-button btn btn-danger">Remove</a></div>
        <div class="form-group">
            {!! Form::label('item_id[' . $key . ']', 'Item') !!}
            {!! Form::select('item_id[' . $key . ']', $items, $stock ? $stock->item_id : null, ['class' => 'form-control stock-field', 'data-name' => 'item_id']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('cost[' . $key . ']', 'Cost') !!}
            <div class="row">
                <div class="col-4">
                    {!! Form::text('cost[' . $key . ']', $stock ? $stock->cost : null, ['class' => 'form-control stock-field', 'data-name' => 'cost']) !!}
                </div>
                <div class="col-8">
                    {!! Form::select('currency_id[' . $key . ']', $currencies, $stock ? $stock->currency_id : null, ['class' => 'form-control stock-field', 'data-name' => 'currency_id']) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::checkbox('use_user_bank[' . $key . ']', 1, $stock ? $stock->use_user_bank : 1, ['class' => 'form-check-input stock-toggle stock-field', 'data-name' => 'use_user_bank']) !!}
                    {!! Form::label('use_user_bank[' . $key . ']', 'Use User Bank', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow users to purchase the item using the currency in their accounts, provided that users can own that currency.') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    {!! Form::checkbox('use_character_bank[' . $key . ']', 1, $stock ? $stock->use_character_bank : 1, ['class' => 'form-check-input stock-toggle stock-field', 'data-name' => 'use_character_bank']) !!}
                    {!! Form::label('use_character_bank[' . $key . ']', 'Use Character Bank', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow users to purchase the item using the currency belonging to characters they own, provided that characters can own that currency.') !!}
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::checkbox('is_limited_stock[' . $key . ']', 1, $stock ? $stock->is_limited_stock : false, ['class' => 'form-check-input stock-limited stock-toggle stock-field', 'data-name' => 'is_limited_stock']) !!}
            {!! Form::label('is_limited_stock[' . $key . ']', 'Set Limited Stock', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned on, will limit the amount purchaseable to the quantity set below.') !!}
        </div>
        <div class="card mb-3 stock-limited-quantity {{ $stock && $stock->is_limited_stock ? '' : 'hide' }}">
            <div class="card-body">
                <div>
                    {!! Form::label('quantity[' . $key . ']', 'Quantity') !!} {!! add_help('If left blank, will be set to 0 (sold out).') !!}
                    {!! Form::text('quantity[' . $key . ']', $stock ? $stock->quantity : 0, ['class' => 'form-control stock-field', 'data-name' => 'quantity']) !!}
                </div>
            </div>
        </div>
        <div>
            {!! Form::label('purchase_limit[' . $key . ']', 'User Purchase Limit') !!} {!! add_help('This is the maximum amount of this item a user can purchase from this shop. Set to 0 to allow infinite purchases.') !!}
            {!! Form::text('purchase_limit[' . $key . ']', $stock ? $stock->purchase_limit : 0, ['class' => 'form-control stock-field', 'data-name' => 'purchase_limit']) !!}
        </div>
    </div>
</div>
