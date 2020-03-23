<tr class="{{ ($log->recipient_id == $owner->id && $log->recipient_type == $owner->logType) ? 'inflow' : 'outflow' }}">
    <td>{!! $log->sender ? $log->sender->displayName : '' !!}</td>
    <td>{!! $log->recipient ? $log->recipient->displayName : '' !!}</td>
    <td>{{ ($log->recipient_id == $owner->id && $log->recipient_type == $owner->logType) ? '+' : '-' }} {!! $log->currency ? $log->currency->display(abs($log->quantity)) : $log->cost . ' (Deleted Currency)' !!}</td>
    <td>{!! $log->log !!}</td>
    <td>{!! format_date($log->created_at) !!}</td>
</tr>