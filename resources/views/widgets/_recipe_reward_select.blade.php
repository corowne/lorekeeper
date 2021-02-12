<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addReward">Add Reward</a>
</div>
<table class="table table-sm" id="rewardTable">
    <thead>
        <tr>
            <th width="35%">Reward Type</th>
            <th width="35%">Reward</th>
            <th width="20%">Quantity</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="rewardTableBody">
        @if($rewards)
            @foreach($rewards as $reward)
                <tr class="reward-row">
                    <td>{!! Form::select('rewardable_type[]', ['Item' => 'Item', 'Currency' => 'Currency', 'LootTable' => 'Loot Table', 'Raffle' => 'Raffle'], $reward->rewardable_type, ['class' => 'form-control reward-type selectize', 'placeholder' => 'Select Reward Type']) !!}</td>
                    <td class="reward-row-select">
                        @if($reward->rewardable_type == 'Item')
                            {!! Form::select('rewardable_id[]', $items, $reward->rewardable_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                        @elseif($reward->rewardable_type == 'Currency')
                            {!! Form::select('rewardable_id[]', $currencies, $reward->rewardable_id, ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                        @elseif($reward->rewardable_type == 'LootTable')
                            {!! Form::select('rewardable_id[]', $tables, $reward->rewardable_id, ['class' => 'form-control table-select selectize', 'placeholder' => 'Select Loot Table']) !!}
                        @elseif($reward->rewardable_type == 'Raffle')
                            {!! Form::select('rewardable_id[]', $raffles, $reward->rewardable_id, ['class' => 'form-control raffle-select selectize', 'placeholder' => 'Select Raffle']) !!}
                        @endif
                    </td>
                    <td>{!! Form::text('reward_quantity[]', $reward->quantity, ['class' => 'form-control']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-reward-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>