<tr class="{{ ($exp->recipient_id == $user->id) ? 'inflow' : 'outflow' }}">
    <td><i class="btn py-1 m-0 px-2 btn-{{ ($exp->quantity > 0 ) ? 'success' : 'danger'}} fas {{ ($exp->quantity > 0 ) ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
      {!! $exp->sender ? $exp->sender->displayName : '' !!}
    </td>
    <td>{!! $exp->recipient ? $exp->recipient->displayName : '' !!}</div>
    <td>{{  $exp->quantity }}</div>
    <td>{!! $exp->log !!}</div>
    <td>{!! pretty_date($exp->created_at) !!}</div>
</tr>
  