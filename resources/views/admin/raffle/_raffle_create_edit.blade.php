@if(!$raffle->id)
    <p>
        Enter basic information about this raffle. Tickets can be added after the raffle is created.
    </p>
@endif
{!! Form::open(['url' => 'admin/raffles/edit/raffle/'.($raffle->id ? : '')]) !!}
    <div class="form-group">
        {!! Form::label('name', 'Raffle name') !!} {!! add_help('This is the name of the raffle. Naming it something after what is being raffled is suggested (does not have to be unique).') !!}
        {!! Form::text('name', $raffle->name, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('winner_count', 'Number of winners to draw') !!}
        {!! Form::text('winner_count', $raffle->winner_count, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('group_id', 'Raffle group') !!} {!! add_help('Raffle groups must be created before you can select them here.') !!}
        {!! Form::select('group_id', $groups, $raffle->group_id, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('order', 'Raffle order') !!} {!! add_help('Enter a number. If a group of raffles is rolled, raffles will be drawn in ascending order.') !!}
        {!! Form::text('order', $raffle->order ? : 0, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        <label class="control-label">
            {!! Form::checkbox('is_active', 1, $raffle->is_active, ['class' => 'form-check-input mr-2', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_displayed', 'Active (visible to users)', ['class' => 'form-check-label ml-3']) !!}
        </label>
    </div>
    <div class="text-right">
        {!! Form::submit('Confirm', ['class' => 'btn btn-secondary']) !!}
    </div>
{!! Form::close() !!}