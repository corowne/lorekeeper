<p>You rolled {{ $quantity }} time{{ $quantity != 1 ? 's' : '' }} for the following:</p>
<table class="table table-sm table-striped">
    <thead>
        <th>#</th>
        <th>Encounter Rolled</th>
    </thead>
    <tbody>
        <?php $count = 1; ?>
        @foreach($results as $result)
            @foreach($result as $type)
                @if(count($type))
                    @foreach($type as $t)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{!! $t['asset']->displayName !!}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>