<tr class="{{ ($count->recipient_id == $character->id) ? 'inflow' : 'outflow' }}">
    <td><i class="btn py-1 m-0 px-2 btn-{{ ($count->quantity > 0 ) ? 'success' : 'danger'}} fas {{ ($count->quantity > 0 ) ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
      {!! $count->sender ? $count->sender->displayName : '' !!}
    </td>
    <td>{!! $count->recipient ? $count->recipient->displayName : '' !!}</div>
    <td>{{  $count->quantity }}</div>
    <td>{!! $count->log !!}</div>
    <td>{!! pretty_date($count->created_at) !!}</div>
</tr>
  