<div class="row flex-wrap">
    <div class="col-12 col-md-2"><div class="logs-table-cell">{!! $log->item ? $log->item->displayName : '(Deleted Item)' !!}</div></div>
    <div class="col-12 col-md-2"><div class="logs-table-cell">{!! $log->quantity !!}</div></div>
    <div class="col-12 col-md-2"><div class="logs-table-cell">{!! $log->shop ? $log->shop->displayName : '(Deleted Shop)' !!}</div></div>
    <div class="col-12 col-md-2"><div class="logs-table-cell">{!! $log->character_id ? $log->character->displayName : '' !!}</div></div>
    <div class="col-12 col-md-2"><div class="logs-table-cell">{!! $log->currency ? $log->currency->display($log->cost) : $log->cost . ' (Deleted Currency)' !!}</div></div>
    <div class="col-12 col-md-2"><div class="logs-table-cell">{!! pretty_date($log->created_at) !!}</div></div>
</div>
