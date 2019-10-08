<tr class="inflow">
    <td>{!! $log->sender ? $log->sender->displayName : '' !!}</td>
    <td>{!! $log->log !!}</td>
    <td>{{ format_date($log->created_at) }}</td>
</tr>