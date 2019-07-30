<p>These are the default rewards for this prompt. Note that the actual rewards you receive may be edited by a staff member during the approval process.</p>
@if($count)
    <p>You have completed this prompt <strong>{{ $count }}</strong> time{{ $count == 1 ? '' : 's' }}.</p>
@endif
<table class="table table-sm">
    <thead>
        <tr>
            <th width="70%">Reward</th>
            <th width="30%">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prompt->rewards as $reward)
            <tr>
                <td>{!! $reward->reward->displayName !!}</td>
                <td>{{ $reward->quantity }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="#" class="btn btn-primary add-prompt-reward">Add Reward</a>
</div>