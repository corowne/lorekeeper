<tr class="outflow">
    <td>{!! $log->item->displayName !!}</td>
    <td>{!! $log->shop->displayName !!}</td>
    <td>{!! $log->character_id ? $log->character->displayName : '' !!}</td>
    <td>{!! $log->currency->display($log->cost) !!}</td>
    <td>{!! format_date($log->created_at) !!}</td>
</tr>