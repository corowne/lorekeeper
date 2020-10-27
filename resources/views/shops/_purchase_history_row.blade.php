<tr class="outflow">
    <td>{!! $log->item ? $log->item->displayName : '(Deleted Item)' !!}</td>
    <td>{!! $log->shop ? $log->shop->displayName : '(Deleted Shop)' !!}</td>
    <td>{!! $log->character_id ? $log->character->displayName : '' !!}</td>
    <td>{!! $log->currency ? $log->currency->display($log->cost) : $log->cost . ' (Deleted Currency)' !!}</td>
    <td>{!! format_date($log->created_at) !!}</td>
</tr>