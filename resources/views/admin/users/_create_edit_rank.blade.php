@if($rank)
    {!! Form::open(['url' => $rank->id ? 'admin/users/ranks/edit/'.$rank->id : 'admin/users/ranks/create']) !!}

    <div class="form-group">
        {!! Form::label('Rank Name') !!}
        {!! Form::text('name', $rank->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Description (optional)') !!}
        {!! Form::textarea('description', $rank->description, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Colour (Hex code; optional)') !!}
        <div class="input-group cp">
            {!! Form::text('color', $rank->color, ['class' => 'form-control']) !!}
            <span class="input-group-append">
                <span class="input-group-text colorpicker-input-addon"><i></i></span>
            </span>
        </div>
    </div>

    @if($editable != 2)
        {{-- Powers --}}
        <div class="form-group">
            <div class="row">
                @foreach($powers as $key => $power)
                    <div class="col-md-6">
                        <div class="form-check">
                            {!! Form::checkbox('powers[]', $key, $rankPowers ? isset($rankPowers[$key]) : false, ['class' => 'form-check-input']) !!}
                            {!! Form::label('powers[]', $power['name'] , ['class' => 'form-check-label']) !!}
                            {!! add_help($power['description']) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else 
        <div class="card bg-light mb-3">
            <div class="card-body">Powers for the admin rank cannot be edited. {!! add_help('The admin rank has the ability to edit any editable information on the site, and is always highest-ranked (cannot be edited by any other user).') !!}</div>
        </div>
    @endif

    <div class="text-right">
        {!! Form::submit($rank->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid rank selected.
@endif