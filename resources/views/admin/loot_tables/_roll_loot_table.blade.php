<p>You rolled {{ $quantity }} time{{ $quantity != 1 ? 's' : '' }} for the following:</p>
<div class="mb-4 logs-table table-striped">
    <div class="logs-table-header">
        <div class="row">
            <div class="col-1">
                <div class="logs-table-cell text-center">#</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="logs-table-cell">Reward</div>
            </div>
            <div class="col-5 col-md-3">
                <div class="logs-table-cell">Quantity</div>
            </div>
        </div>
    </div>
    <div class="logs-table-body">
        <?php $count = 1; ?>
        @foreach ($results as $result)
            @foreach ($result as $type)
                @if (count($type))
                    @foreach ($type as $t)
                        <div class="logs-table-row">
                            <div class="row flex-wrap">
                                <div class="col-1">
                                    <div class="logs-table-cell text-center">{{ $count++ }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="logs-table-cell">{!! $t['asset']->displayName !!}</div>
                                </div>
                                <div class="col-5 col-md-3">
                                    <div class="logs-table-cell">{{ $t['quantity'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endforeach
        @endforeach
    </div>
</div>
<p>Note: "None" results are not shown in this table.</p>
