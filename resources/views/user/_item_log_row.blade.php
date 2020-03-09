<tr class="{{ ($log->recipient_id == $user->id) ? 'inflow' : 'outflow' }}">
    <td>{!! $log->sender ? $log->sender->displayName : '' !!}</td>
    <td>{!! $log->recipient ? $log->recipient->displayName : '' !!}</td>
    <td>{!! $log->item->displayName !!} (×{!! $log->quantity !!})</td>
    <td>{!! $log->log !!}</td>
    <td>{!! format_date($log->created_at) !!}</td>
</tr>