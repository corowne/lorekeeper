<div class="card mb-3">
    <div class="card-header">
        <h2 class="mb-0">
            <span class="float-right badge badge-{{ $trade->status == 'Pending' || $trade->status == 'Open' || $trade->status == 'Canceled' ? 'secondary' : ($trade->status == 'Completed' ? 'success' : 'danger') }}">{{ $trade->status }}</span>
            <a href="{{ $trade->url }} ">Trade (#{{ $trade->id }})</a>
        </h2>
        @if ($trade->staff)
            Proccessed by {!! $trade->staff->displayName !!}
        @endif
    </div>
    <div class="card-body">
        @if ($trade->comments)
            <div>{!! nl2br(htmlentities($trade->comments)) !!}</div>
            <hr />
        @endif
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-heading">
                    {!! $trade->sender->id == Auth::user()->id ? 'Your Offer' : $trade->sender->displayName . '\'s Offer' !!}
                    <span class="float-right">
                        @if ($trade->{'is_sender_confirmed'})
                            @if ($trade->{'is_sender_trade_confirmed'})
                                <small class="text-success">Trade Confirmed</small>
                            @else
                                <small class="text-primary">Offer Confirmed</small>
                            @endif
                        @else
                            <small class="text-muted">Pending</small>
                        @endif
                    </span>
                </h3>
                @include('home.trades._offer_summary', [
                    'user' => $trade->sender,
                    'data' => isset($trade->data['sender']) ? parseAssetData($trade->data['sender']) : null,
                    'trade' => $trade,
                    'stacks' => isset($trade->stacks['sender']) ? $trade->stacks['sender'] : [],
                ])
            </div>
            <div class="col-md-6">
                <h3 class="card-heading">
                    {!! $trade->recipient->id == Auth::user()->id ? 'Your Offer' : $trade->recipient->displayName . '\'s Offer' !!}
                    <span class="float-right">
                        @if ($trade->{'is_recipient_confirmed'})
                            @if ($trade->{'is_recipient_trade_confirmed'})
                                <small class="text-success">Trade Confirmed</small>
                            @else
                                <small class="text-primary">Offer Confirmed</small>
                            @endif
                        @else
                            <small class="text-muted">Pending</small>
                        @endif
                    </span>
                </h3>
                @include('home.trades._offer_summary', [
                    'user' => $trade->recipient,
                    'data' => isset($trade->data['recipient']) ? parseAssetData($trade->data['recipient']) : null,
                    'trade' => $trade,
                    'stacks' => isset($trade->stacks['recipient']) ? $trade->stacks['recipient'] : [],
                ])
            </div>
        </div>
        <hr />
        <div class="text-right">
            <a href="{{ $trade->url }}" class="btn btn-outline-primary">View Details</a>
            @if (isset($queueView) && $trade->status == 'Pending')
                @if (!$trade->is_approved)
                    <a href="#" class="btn btn-outline-success trade-action-button" data-id="{{ $trade->id }}" data-action="approve">Approve</a>
                    <a href="#" class="btn btn-outline-danger trade-action-button" data-id="{{ $trade->id }}" data-action="reject">Reject</a>
                @else
                    Currently awaiting mod approval
                @endif
            @endif
        </div>
    </div>
</div>
