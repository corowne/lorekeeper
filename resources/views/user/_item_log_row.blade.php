<tr class="{{ ($log->recipient_id == $owner->id && $log->recipient_type == $owner->logType) ? 'inflow' : 'outflow' }}">
    <td>{!! $log->sender ? $log->sender->displayName : '' !!}</td>
    <td>{!! $log->recipient ? $log->recipient->displayName : '' !!}</td>
    <td>{{ ($log->recipient_id == $owner->id && $log->recipient_type == $owner->logType) ? '+' : '-' }} {!! $log->item ? $log->item->displayName : '(Deleted Item)' !!} (×{!! $log->quantity !!})</td>
    <td>{!! $log->log !!}</td>
    <td>{!! format_date($log->created_at) !!}</td>
</tr>