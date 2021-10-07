<div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
  <div class="col-6 col-md-2">
    <i class="btn py-1 m-0 px-2 btn-{{ ($log->recipient_id == $owner->id) ? 'success' : 'danger' }} fas {{ ($log->recipient_id == $owner->id && $log->recipient_type == $owner->logType) ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
    {!! $log->sender ? $log->sender->displayName : '' !!}
  </div>
  <div class="col-6 col-md-2">{!! $log->recipient ? $log->recipient->displayName : '' !!}</div>
  <div class="col-6 col-md-2">{!! $log->recipe ? $log->recipe->displayName : '(Deleted Recipe)' !!}</div>
  <div class="col-6 col-md-4">{!! $log->log !!}</div>
  <div class="col-6 col-md-2">{!! pretty_date($log->created_at) !!}</div>
</div>
