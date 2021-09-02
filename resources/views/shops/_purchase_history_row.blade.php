<div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
  <div class="col-12 col-md-2">{!! $log->item ? $log->item->displayName : '(Deleted Item)' !!}</div>
  <div class="col-12 col-md-2">{!! $log->quantity !!}</div>
  <div class="col-12 col-md-2">{!! $log->shop ? $log->shop->displayName : '(Deleted Shop)' !!}</div>
  <div class="col-12 col-md-2">{!! $log->character_id ? $log->character->displayName : '' !!}</div>
  <div class="col-12 col-md-2">{!! $log->currency ? $log->currency->display($log->cost) : $log->cost . ' (Deleted Currency)' !!}</div>
  <div class="col-12 col-md-2">{!! pretty_date($log->created_at) !!}</div>
</div>
