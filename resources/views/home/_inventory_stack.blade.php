@if(!$stack)
    <div class="text-center">Invalid item selected.</div>
@else
    <div class="text-center">
        <div class="mb-1"><a href="{{ $stack->item->url }}"><img src="{{ $stack->item->imageUrl }}" /></a></div>
        <div @if(count($stack->item->tags)) class="mb-1" @endif><a href="{{ $stack->item->url }}">{{ $stack->item->name }}</a></div>
        @if(count($stack->item->tags))
            <div>
                @foreach($stack->item->tags as $tag)
                    @if($tag->is_active)
                        {!! $tag->displayTag !!}
                    @endif
                @endforeach
            </div>
        @endif
    </div>
    
    @if(isset($stack->data['notes']) || isset($stack->data['data']))
        <div class="card mt-3">
            <ul class="list-group list-group-flush">
                @if(isset($stack->data['notes']))
                    <li class="list-group-item">
                        <h5 class="card-title">Notes</h5>
                        <div>{!! $stack->data['notes'] !!}</div>
                    </li>
                @endif
                @if(isset($stack->data['data']))
                    <li class="list-group-item">
                        <h5 class="card-title">Source</h5>
                        <div>{!! $stack->data['data'] !!}</div>
                    </li>
                @endif
            </ul>
        </div>
    @endif

    @if($user && !$readOnly && ($stack->user_id == $user->id || $user->hasPower('edit_inventories')))
        <div class="card mt-3">
            <ul class="list-group list-group-flush">
                @if(count($stack->item->tags))
                    @foreach($stack->item->tags as $tag)
                        @if(View::exists('inventory._'.$tag->tag))
                            @include('inventory._'.$tag->tag, ['stack' => $stack, 'tag' => $tag])
                        @endif
                    @endforeach
                @endif
                @if($stack->isTransferrable || $user->hasPower('edit_inventories'))
                    <li class="list-group-item">
                        <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#transferForm">@if($stack->user_id != $user->id) [ADMIN] @endif Transfer Item</a>
                        {!! Form::open(['url' => 'inventory/transfer/'.$stack->id, 'id' => 'transferForm', 'class' => 'collapse']) !!}
                            @if(!$stack->isTransferrable)
                                <p class="alert alert-warning my-2">This item is account-bound, but your rank allows you to transfer it to another user.</p>
                            @endif
                            <div class="form-group">
                                {!! Form::label('user_id', 'Recipient') !!} {!! add_help('You can only transfer items to verified users.') !!}
                                {!! Form::select('user_id', $userOptions, null, ['class'=>'form-control']) !!}
                            </div>
                            <div class="text-right">
                                {!! Form::submit('Transfer', ['class' => 'btn btn-primary']) !!}
                            </div>
                        {!! Form::close() !!}
                    </li>
                @else
                    <li class="list-group-item bg-light">
                        <h5 class="card-title mb-0 text-muted"><i class="fas fa-lock mr-2"></i> Account-bound</h5>
                    </li>
                @endif
                <li class="list-group-item">
                    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#deleteForm">@if($stack->user_id != $user->id) [ADMIN] @endif Delete Item</a>
                    {!! Form::open(['url' => 'inventory/delete/'.$stack->id, 'id' => 'deleteForm', 'class' => 'collapse']) !!}
                        <p>This action is not reversible. Are you sure you want to delete this item?</p>
                        <div class="text-right">
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        </div>
                    {!! Form::close() !!}
                </li>
            </ul>
        </div>
    @endif
@endif