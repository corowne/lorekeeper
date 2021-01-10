<tr class="{{ ($stat->recipient_id == $user->id) ? 'inflow' : 'outflow' }}">
    <td><i class="btn py-1 m-0 px-2 btn-{{ ($stat->quantity > 0 ) ? 'success' : 'danger'}} fas {{ ($stat->quantity > 0 ) ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
      {!! $stat->sender ? $stat->sender->displayName : '' !!}
    </td>
    <td>{!! $stat->recipient ? $stat->recipient->displayName : '' !!}</div>
    <td>{{  $stat->quantity }}</div>
    <td>{!! $stat->log !!}</div>
    <td>{!! pretty_date($stat->created_at) !!}</div>
</tr>
  