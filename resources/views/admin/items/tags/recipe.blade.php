<h3>Rewards</h3>

<p>These are the rewards that will be distributed to the user when they use the box from their inventory. The box will only distribute rewards to the user themselves - character-only currencies should not be added.</p>



<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addLoot">Add Reward</a>
</div>
<table class="table table-sm" id="lootTable">
    <thead>
        <tr>
            <th width="35%">Reward</th>
            <th width="20%">Quantity</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="lootTableBody">
        @if($tag->getData())
            @foreach($tag->getData() as $loot)
                <tr class="loot-row">
                    <td class="loot-row-select">
                            {!! Form::select('rewardable_id[]', $recipes, $loot->rewardable_id, ['class' => 'form-control recipe-select selectize', 'placeholder' => 'Select Recipe']) !!}
                    </td>
                    <td>{!! Form::text('quantity[]', $loot->quantity, ['class' => 'form-control']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-loot-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
