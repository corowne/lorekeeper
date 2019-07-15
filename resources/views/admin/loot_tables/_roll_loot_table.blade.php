<p>You rolled {{ $quantity }} time{{ $quantity != 1 ? 's' : '' }} for the following:</p>
<table class="table table-sm table-striped">
    <thead>
        <th>#</th>
        <th>Reward</th>
        <th>Quantity</th>
    </thead>
    <tbody>
        <?php $count = 1; ?>
        @foreach($results as $result)
            @foreach($result as $type)
                @if(count($type))
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td>{!! $type[0]['asset']->displayName !!}</td>
                        <td>{{ $type[0]['quantity'] }}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>