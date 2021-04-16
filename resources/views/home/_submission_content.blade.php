<h1>
    {{ $submission->prompt_id ? 'Submission' : 'Claim' }} (#{{ $submission->id }})
    <span class="float-right badge badge-{{ $submission->status == 'Pending' ? 'secondary' : ($submission->status == 'Approved' ? 'success' : 'danger') }}">{{ $submission->status }}</span>
</h1>

<div class="mb-1">
    <div class="row">
        <div class="col-md-2 col-4"><h5>User</h5></div>
        <div class="col-md-10 col-8">{!! $submission->user->displayName !!}</div>
    </div>
    @if($submission->prompt_id)
        <div class="row">
            <div class="col-md-2 col-4"><h5>Prompt</h5></div>
            <div class="col-md-10 col-8">{!! $submission->prompt->displayName !!}</div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-2 col-4"><h5>URL</h5></div>
        <div class="col-md-10 col-8"><a href="{{ $submission->url }}">{{ $submission->url }}</a></div>
    </div>
    <div class="row">
        <div class="col-md-2 col-4"><h5>Submitted</h5></div>
        <div class="col-md-10 col-8">{!! format_date($submission->created_at) !!} ({{ $submission->created_at->diffForHumans() }})</div>
    </div>
    @if($submission->status != 'Pending')
        <div class="row">
            <div class="col-md-2 col-4"><h5>Processed</h5></div>
            <div class="col-md-10 col-8">{!! format_date($submission->updated_at) !!} ({{ $submission->updated_at->diffForHumans() }}) by {!! $submission->staff->displayName !!}</div>
        </div>
    @endif
</div>
<h2>Comments</h2>
<div class="card mb-3"><div class="card-body">{!! nl2br(htmlentities($submission->comments)) !!}</div></div>
@if(Auth::check() && $submission->staff_comments && ($submission->user_id == Auth::user()->id || Auth::user()->hasPower('manage_submissions')))
    <h2>Staff Comments</h2>
    <div class="card mb-3"><div class="card-body">
	    @if(isset($submission->parsed_staff_comments))
            {!! $submission->parsed_staff_comments !!}
        @else
            {!! $submission->staff_comments !!}
        @endif
		</div></div>
@endif

<h2>Rewards</h2>
<table class="table table-sm">
    <thead>
        <tr>
            <th width="70%">Reward</th>
            <th width="30%">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach(parseAssetData( isset($submission->data['rewards']) ? $submission->data['rewards'] : $submission->data ) as $type)
            @foreach($type as $asset)
                <tr>
                    <td>{!! $asset['asset']->displayName !!}</td>
                    <td>{{ $asset['quantity'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

@if($submission->prompt_id && $submission->prompt->expreward)
<h3>Stat & Level Rewards</h3>
    <div class="card m-1">
        <div class="row m-2">
            <div class="col">
                <h5>User Rewards</h5>
                @if(!$submission->prompt->expreward->user_exp && !$submission->prompt->expreward->user_points)
                No user rewards.
                @else
                {{ $submission->prompt->expreward->user_exp ? $submission->prompt->expreward->user_exp : 0  }} user EXP
                    <br>
                {{ $submission->prompt->expreward->user_points ? $submission->prompt->expreward->user_points : 0  }} user points
                @endif
            </div>
            <div class="col">
                <h5>Character Rewards</h5>
                @if(!$submission->prompt->expreward->chara_exp && !$submission->prompt->expreward->chara_points)
                No character rewards.
                @else
                {{ $submission->prompt->expreward->chara_exp ? $submission->prompt->expreward->chara_exp : 0  }} character EXP
                    <br>
                {{ $submission->prompt->expreward->chara_points ? $submission->prompt->expreward->chara_points : 0  }} character points
                @endif
            </div>
        </div>
        @if($submission->bonus)
        <hr>
        @php
            $bonus = json_decode($submission->bonus, true);
        @endphp
        <div class="m-2">
            <h4 class="m-2">Bonus Rewards</h4>
            <hr>
            <div class="row m-2">
                <div class="col">
                    <h5>User Rewards</h5>
                    {{ $bonus[0]['User_Bonus']['exp'] ? $bonus[0]['User_Bonus']['exp'] : 'No bonus'}} user EXP
                        <br>
                    {{ $bonus[0]['User_Bonus']['points'] ? $bonus[0]['User_Bonus']['points'] : 'No bonus'}} user points
                </div>
                <div class="col">
                    <h5>Character Rewards</h5>
                    {{ $bonus[0]['Character_Bonus']['exp'] ? $bonus[0]['Character_Bonus']['exp'] : 'No bonus'}} character EXP
                        <br>
                    {{ $bonus[0]['Character_Bonus']['points'] ? $bonus[0]['Character_Bonus']['points'] : 'No bonus'}} character points
                </div>
            </div>
        </div>
        @endif
    </div>
@endif

<h2>Characters</h2>
@if($submission->focus_chara_id)
    <h5>Focus Character</h5>
    <div class="submission-character-row mb-2">
        <div class="submission-character-thumbnail"><a href="{{ $submission->focus->url }}"><img src="{{ $submission->focus->image->thumbnailUrl }}" class="img-thumbnail" /></a></div>
        @if($submission->prompt_id)
        <div class="submission-character-info card ml-2">
            <div class="card-body">
                <div class="submission-character-info-content">
                    <h3 class="mb-2 submission-character-info-header"><a href="{{ $submission->focus->url }}">{{ $submission->focus->fullName }}</a></h3>
                    <div class="submission-character-info-body">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Reward</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $submission->prompt->chara_exp ? $submission->prompt->chara_exp : 0 }} EXP
                                <br>
                                {{ $submission->prompt->chara_points ? $submission->prompt->chara_points : 0  }} Stat Point
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
<hr>
 @endif
@foreach($submission->characters as $character)
    <div class="submission-character-row mb-2">
        <div class="submission-character-thumbnail"><a href="{{ $character->character->url }}"><img src="{{ $character->character->image->thumbnailUrl }}" class="img-thumbnail" /></a></div>
        <div class="submission-character-info card ml-2">
            <div class="card-body">
                <div class="submission-character-info-content">
                    <h3 class="mb-2 submission-character-info-header"><a href="{{ $character->character->url }}">{{ $character->character->fullName }}</a></h3>
                    <div class="submission-character-info-body">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th width="70%">Reward</th>
                                <th width="30%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(parseAssetData($character->data) as $key => $type)

                                @foreach($type as $asset)
                                    <tr>
                                        <td>{!! $asset['asset']->displayName !!} ({!! ucfirst($key) !!})</td>
                                        <td>{{ $asset['quantity'] }}</td>
                                    </tr>
                                @endforeach
                            @endforeach

                            {{--

                            If you want to "Categorize" the rewards by type, uncomment this and comment or remove the above @foreach.

                            @foreach(parseAssetData($character->data) as $key => $type)
                                @if(count($type))
                                <tr><td colspan="2"><strong>{!! strtoupper($key) !!}</strong></td></tr>
                                    @foreach($type as $asset)
                                        <tr>
                                            <td>{!! $asset['asset']->displayName !!}</td>
                                            <td>{{ $asset['quantity'] }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach

                            --}}
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if(isset($inventory['user_items']))
<h2>Add-Ons</h2>
<p>These items have been removed from the {{ $submission->prompt_id ? 'submitter' : 'claimant' }}'s inventory and will be refunded if the request is rejected or consumed if it is approved.</p>
            <table class="table table-sm">
                <thead class="thead-light">
                        <tr class="d-flex">
                            <th class="col-2">Item</th>
                            <th class="col-4">Source</th>
                            <th class="col-4">Notes</th>
                            <th class="col-2">Quantity</th>
                        </tr>
                </thead>
                <tbody>
                    @foreach($inventory['user_items'] as $itemRow)
                        <tr class="d-flex">
                            <td class="col-2">@if(isset($itemsrow[$itemRow['asset']->item_id]->image_url)) <img class="small-icon" src="{{ $itemsrow[$itemRow['asset']->item_id]->image_url }}"> @endif {!! $itemsrow[$itemRow['asset']->item_id]->name !!}
                            <td class="col-4">{!! array_key_exists('data', $itemRow['asset']->data) ? ($itemRow['asset']->data['data'] ? $itemRow['asset']->data['data'] : 'N/A') : 'N/A' !!}</td>
                            <td class="col-4">{!! array_key_exists('notes', $itemRow['asset']->data) ? ($itemRow['asset']->data['notes'] ? $itemRow['asset']->data['notes'] : 'N/A') : 'N/A' !!}</td>
                            <td class="col-2">{!! $itemRow['quantity'] !!}
                        </tr>
                    @endforeach
                </tbody>
            </table>
@endif

@if(isset($inventory['currencies']))
    <h3>{!! $submission->user->displayName !!}'s Bank</h3>
    <table class="table table-sm mb-3">
        <thead>
            <tr>
                <th width="70%">Currency</th>
                <th width="30%">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventory['currencies'] as $currency)
                <tr>
                    <td>{!! $currency['asset']->name !!}</td>
                    <td>{{ $currency['quantity'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
