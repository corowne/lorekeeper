<p>You rolled {{ $quantity }} time{{ $quantity != 1 ? 's' : '' }} for the following:</p>
<table class="table table-sm table-striped">
    <thead>
        <th>#</th>
        <th>Encounter Rolled</th>
    </thead>
    <tbody>
        <?php $count = 1; ?>
        @foreach ($results as $result)
            <tr>
                <td>{{ $count++ }}</td>
                <td>{!! \App\Models\Encounter\Encounter::find($result->encounter_id)->name !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>
