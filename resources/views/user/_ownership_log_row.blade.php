<tr class="{{ (!$user || $log->recipient_id == $user->id || $log->recipient_alias == $user->alias) ? 'inflow' : 'outflow' }}">
    <td>{!! $log->sender ? $log->sender->displayName : '' !!}</td>
    <td>{!! $log->recipient ? $log->recipient->displayName : $log->displayRecipientAlias !!}</td>
    @if(isset($showCharacter))
        <td>{!! $log->character ? $log->character->displayName : '---' !!}</td>
    @endif
    <td>{!! $log->log !!}</td>
    <td>{{ format_date($log->created_at) }}</td>
</tr>