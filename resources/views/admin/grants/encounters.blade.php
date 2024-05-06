@extends('admin.layout')

@section('admin-title')
    Grant Encounter Energy
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Grant Encounter Energy' => 'admin/grants/encounter-energy']) !!}

    <h1>Grant Encounter Energy</h1>

    {!! Form::open(['url' => 'admin/grants/encounter-energy']) !!}

    <h3>Basic Information</h3>

    @if (Config::get('lorekeeper.encounters.use_characters'))
        <div class="form-group">
            {!! Form::label('character_names[]', 'Character(s)') !!} {!! add_help('You can select up to 10 users at once.') !!}
            {!! Form::select('character_names[]', $characterOptions, null, ['id' => 'characterList', 'class' => 'form-control', 'multiple']) !!}
        </div>
    @else
        <div class="form-group">
            {!! Form::label('names[]', 'Username(s)') !!} {!! add_help('You can select up to 10 users at once.') !!}
            {!! Form::select('names[]', $users, null, ['id' => 'usernameList', 'class' => 'form-control', 'multiple']) !!}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('quantity', 'Quantity') !!} {!! add_help('If the value given is less than 0, this amount will be deducted.') !!}
                {!! Form::text('quantity', null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    <script>
        $(document).ready(function() {
            $('#usernameList').selectize({
                maxItems: 10
            });
            $('#characterList').selectize({
                maxItems: 10
            });
        });
    </script>
@endsection
