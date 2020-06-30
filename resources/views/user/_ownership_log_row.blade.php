
<div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
  <div class="col-6 col-md-2">
    <i class="btn py-1 m-0 px-2 btn-{{ (!$user || $log->recipient_id == $user->id || $log->recipient_alias == $user->alias) ? 'success' : 'danger' }} fas {{ (!$user || $log->recipient_id == $user->id || $log->recipient_alias == $user->alias) ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
    {!! $log->sender ? $log->sender->displayName : '' !!}
  </div>
  <div class="col-6 col-md-2">{!! $log->recipient ? $log->recipient->displayName : $log->displayRecipientAlias !!}</div>
  <div class="col-6 col-md-2">
    @if(isset($showCharacter))
        <td>{!! $log->character ? $log->character->displayName : '---' !!}</td>
    @endif
  </div>
  <div class="col-6 col-md-4">{!! $log->log !!}</div>
  <div class="col-6 col-md-2">{!! pretty_date($log->created_at) !!}</div>
</div>
