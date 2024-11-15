@extends('admin.layout')

@section('admin-title')
    {{ $currency->id ? 'Edit' : 'Create' }} Currency
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Currencies' => 'admin/data/currencies', ($currency->id ? 'Edit' : 'Create') . ' Currency' => $currency->id ? 'admin/data/currencies/edit/' . $currency->id : 'admin/data/currencies/create']) !!}

    <h1>{{ $currency->id ? 'Edit' : 'Create' }} Currency
        @if ($currency->id)
            <a href="#" class="btn btn-danger float-right delete-currency-button">Delete Currency</a>
        @endif
    </h1>

    {!! Form::open(['url' => $currency->id ? 'admin/data/currencies/edit/' . $currency->id : 'admin/data/currencies/create', 'files' => true]) !!}

    <h3>Basic Information</h3>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Currency Name') !!}
                {!! Form::text('name', $currency->name, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Abbreviation (Optional)') !!} {!! add_help('This will be used to denote the currency if an icon is not provided. If an abbreviation is not given, the currency\'s full name will be used.') !!}
                {!! Form::text('abbreviation', $currency->abbreviation, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Icon Image (Optional)') !!} {!! add_help('This will be used to denote the currency. If not provided, the abbreviation will be used.') !!}
                <div class="custom-file">
                    {!! Form::label('icon', 'Choose icon file...', ['class' => 'custom-file-label']) !!}
                    {!! Form::file('icon', ['class' => 'custom-file-input']) !!}
                </div>
                <div class="text-muted">Recommended height: 16px</div>
                @if ($currency->has_icon)
                    <div class="form-check">
                        {!! Form::checkbox('remove_icon', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_icon', 'Remove current image', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
                <div class="custom-file">
                    {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
                    {!! Form::file('image', ['class' => 'custom-file-input']) !!}
                </div>
                <div class="text-muted">Recommended size: 200px x 200px</div>
                @if ($currency->has_image)
                    <div class="form-check">
                        {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $currency->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <h3>Usage</h3>
    <p>Choose whether this currency should be attached to users and/or characters. Both can be selected at the same time, but at least one must be selected.</p>
    <div class="form-group">
        <div class="form-check">
            <label class="form-check-label">
                {!! Form::checkbox('is_user_owned', 1, $currency->is_user_owned, ['class' => 'form-check-input', 'id' => 'userOwned']) !!}
                Attach to Users
            </label>
        </div>
    </div>
    <div class="card mb-3" id="userOptions">
        <div class="card-body">
            <div class="mb-2">
                {!! Form::checkbox('is_displayed', 1, $currency->is_displayed, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_displayed', 'Profile Display', ['class' => 'form-check-label ml-3']) !!} {!! add_help(
                    'If this is on, it will be displayed on users\' main profile pages. Additionally, if the user does not own the currency, it will be displayed as 0 currency. (If this is off, currencies not owned will not be displayed at all.) All owned currencies will still be visible from the user\'s bank page.',
                ) !!}
            </div>
            <div>
                {!! Form::checkbox('allow_user_to_user', 1, $currency->allow_user_to_user, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Allow', 'data-off' => 'Disallow']) !!}
                {!! Form::label('allow_user_to_user', 'User → User Transfers', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow users to transfer this currency to other users from their bank.') !!}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="form-check">
            <label class="form-check-label">
                {!! Form::checkbox('is_character_owned', 1, $currency->is_character_owned, ['class' => 'form-check-input', 'id' => 'characterOwned']) !!}
                Attach to Characters
            </label>
        </div>
    </div>
    <div class="card mb-3" id="characterOptions">
        <div class="card-body">
            <div class="mb-2">
                {!! Form::checkbox('allow_user_to_character', 1, $currency->allow_user_to_character, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Allow', 'data-off' => 'Disallow']) !!}
                {!! Form::label('allow_user_to_character', 'User → Character Transfers', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow a user to transfer this currency to their own characters unidirectionally.') !!}
            </div>
            <div>
                {!! Form::checkbox('allow_character_to_user', 1, $currency->allow_character_to_user, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Allow', 'data-off' => 'Disallow']) !!}
                {!! Form::label('allow_character_to_user', 'Character → User Transfers', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will allow a user to transfer this currency from their own characters to their bank unidirectionally.') !!}
            </div>
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit($currency->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($currency->id)
        <h3>Previews</h3>

        <h5>Display</h5>
        <div class="card mb-3">
            <div class="card-body">
                {!! $currency->display(100) !!}
            </div>
        </div>

        <h5>World Page Entry</h5>
        <div class="card mb-3">
            <div class="card-body">
                @include('world._currency_entry', ['currency' => $currency])
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $userOwned = $('#userOwned');
            var $characterOwned = $('#characterOwned');
            var $userOptions = $('#userOptions');
            var $characterOptions = $('#characterOptions');

            var userOwned = $userOwned.is(':checked');
            var characterOwned = $characterOwned.is(':checked');

            updateOptions();

            $userOwned.on('change', function(e) {
                userOwned = $userOwned.is(':checked');

                updateOptions();
            });
            $characterOwned.on('change', function(e) {
                characterOwned = $characterOwned.is(':checked');

                updateOptions();
            });

            function updateOptions() {
                if (userOwned) $userOptions.removeClass('hide');
                else $userOptions.addClass('hide');

                if (userOwned && characterOwned) $characterOptions.removeClass('hide');
                else $characterOptions.addClass('hide');
            }



            $('.delete-currency-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/currencies/delete') }}/{{ $currency->id }}", 'Delete Currency');
            });
        });
    </script>
@endsection
