@if($log->character && $log->character->is_visible == 1)
<div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
  <div class="col-6 col-md">
    <i class="btn py-1 m-0 px-2 btn-{{ (!$user || $log->recipient_id == $user->id) ? 'success' : 'danger' }} fas {{ (!$user || $log->recipient_id == $user->id) ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
    {!! $log->sender ? $log->sender->displayName : $log->displaySenderAlias !!}
  </div>
  <div class="col-6 col-md">{!! $log->recipient ? $log->recipient->displayName : $log->displayRecipientAlias !!}</div>
  @if(isset($showCharacter))
  <div class="col-6 col-md">
          <td>{!! $log->character ? $log->character->displayName : '---' !!}</td>
    </div>
  @endif
  <div class="col-6 col-md-4">{!! $log->log !!}</div>
  <div class="col-6 col-md">{!! pretty_date($log->created_at) !!}</div>
</div>
@endif
