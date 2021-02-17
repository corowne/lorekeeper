<tr class="{{ ($level->recipient_id == $user->id) ? 'inflow' : 'outflow' }}">
    <td>
      <i class="btn py-1 m-0 px-2 btn-success fas fa-arrow-up mr-2"></i>
      {!! $level->recipient ? $level->recipient->displayName : '' !!}
    </td>
    <td>{!! $level->previous_level !!}</td>
    <td> {{ $level->new_level }}</td>
    <td>{!! pretty_date($level->created_at) !!}</td>
</td>