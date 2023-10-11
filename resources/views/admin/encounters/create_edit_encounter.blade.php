@extends('admin.layout')

@section('admin-title')
    Encounters
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Encounters' => 'admin/data/encounters',
        ($encounter->id ? 'Edit' : 'Create') . ' Encounter' => $encounter->id
            ? 'admin/data/encounters/edit/' . $encounter->id
            : 'admin/data/encounters/create',
    ]) !!}

    <h1>{{ $encounter->id ? 'Edit' : 'Create' }} Encounter
        @if ($encounter->id)
            <a href="#" class="btn btn-danger float-right delete-encounter-button">Delete Encounter</a>
        @endif
    </h1>

    {!! Form::open([
        'url' => $encounter->id ? 'admin/data/encounters/edit/' . $encounter->id : 'admin/data/encounters/create',
        'files' => true,
    ]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $encounter->name, ['class' => 'form-control']) !!}
    </div>

    <div class="row">
        @if ($encounter->has_image)
            <div class="col-md-2">
                <div class="form-group">
                    <img src="{{ $encounter->imageUrl }}" class="img-fluid mr-2 mb-2" style="height: 10em;" />
                    <br>
                </div>
            </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used on the world information pages and side widget.') !!}
                <div>{!! Form::file('image') !!}</div>
                <div class="text-muted">Recommended size: 100px x 100px</div>
                @if ($encounter->has_image)
                    <div class="form-check">
                        {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('initial prompt') !!}{!! add_help('This is the initial prompt the user will see for this encounter.') !!}
        {!! Form::textarea('initial prompt', $encounter->initial_prompt, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_active', 1, $encounter->id ? $encounter->is_active : 1, [
            'class' => 'form-check-input',
            'data-toggle' => 'toggle',
        ]) !!}
        {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help(
            'encounters that are not active will be hidden from the encounter list. They also cannot be automatically set as the next active encounter.',
        ) !!}
    </div>


    <h4>Encounter Options</h4>
    <p>You can create a series of options the user can choose from when they run into this encounter. You can create a short
        option that can be clicked on, as well as a description of the result from the result of that option.</p>
    <p>You can also choose if the user gets rewards or not, they will get the rewards that you choose for this encounter
        below.</p>

    <div id="optionList" class="my-2">
        @if ($encounter->prompts)
            @foreach ($encounter->prompts as $option)
                <div class="my-2">
                    <div class="row">
                        <div class="col-md form-group">
                            {!! Form::label('Option') !!}
                            {!! Form::text('option_name[]', $option->name, [
                                'class' => 'form-control',
                                'placeholder' => 'A short name',
                                'aria-label' => 'Option Name',
                                'aria-describedby' => 'option-name-group',
                            ]) !!}
                        </div>
                        <div class="col-md form-group">
                            {!! Form::checkbox('option_reward[]', 1, $option->give_reward, [
                                'class' => 'form-check-input',
                                'data-name' => 'option_reward',
                            ]) !!}
                            {!! Form::label('option_reward[]', 'Gives Reward?', ['class' => 'form-check-label ml-3']) !!}
                        </div>
                        <div class="col-md form-group">
                            <button class="btn btn-outline-danger remove-option" type="button"
                                id="option-name-group">Remove
                                Option</button>
                        </div>
                    </div>
                    {!! Form::label('Description/Result') !!}
                    {!! Form::textarea('option_description[]', $option->result, [
                        'class' => 'form-control mr-2',
                        'placeholder' =>
                            'Describe the result of this encounter. Ex: The bear was friendly... you missed out on a potential friend!',
                    ]) !!}
                    <hr />
                </div>
            @endforeach
        @endif
    </div>
    <div class="text-right"><a href="#" class="btn btn-primary" id="add-option">Add Option</a></div>

    <h3>Rewards</h3>
    <p>You can add loot tables containing any kind of currencies (both user- and character-attached), but be sure to keep
        track of which are being distributed! Character-only currencies cannot be given to users.</p>
    @include('widgets._loot_select', [
        'loots' => $encounter->rewards,
        'showLootTables' => true,
        'showRaffles' => true,
    ])

    <div class="text-right">
        {!! Form::submit($encounter->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    <div class="option-row hide my-2">
        <div class="row">
            <div class="col-md form-group">
                {!! Form::label('Option') !!}
                {!! Form::text('option_name[]', null, [
                    'class' => 'form-control',
                    'placeholder' => 'What option does the player get to choose? Ex: Run and hide from the bear!',
                    'aria-label' => 'Option name',
                    'aria-describedby' => 'option-name-group',
                ]) !!}
            </div>
            <div class="col-md form-group">
                {!! Form::checkbox('option_reward[]', 1, null, [
                    'class' => 'form-check-input stock-toggle',
                    'data-name' => 'option_reward',
                ]) !!}
                {!! Form::label('option_reward[]', 'Gives Reward?', ['class' => 'form-check-label ml-3']) !!}
            </div>
            <div class="col-md form-group">
                <button class="btn btn-outline-danger remove-option" type="button" id="option-name-group">Remove
                    Option</button>
            </div>
        </div>
        {!! Form::label('Description/Result') !!}
        {!! Form::textarea('option_description[]', null, [
            'class' => 'form-control mr-2',
            'placeholder' =>
                'Describe the result of this encounter. Ex: The bear was friendly... you missed out on a potential friend!',
        ]) !!}
        <hr />
    </div>

    @include('widgets._loot_select_row', [
        'items' => $items,
        'currencies' => $currencies,
        'tables' => $tables,
        'raffles' => $raffles,
        'showLootTables' => true,
        'showRaffles' => true,
    ])

@endsection

@section('scripts')
    @parent
    @include('js._loot_js', ['showLootTables' => true, 'showRaffles' => true])

    <script>
        $(document).ready(function() {
            $('.delete-encounter-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/encounters/delete') }}/{{ $encounter->id }}",
                    'Delete Encounter');
            });

            $('#add-option').on('click', function(e) {
                e.preventDefault();
                addOptionRow();
            });
            $('.remove-option').on('click', function(e) {
                e.preventDefault();
                removeOptionRow($(this));
            })

            function addOptionRow() {
                var $clone = $('.option-row').clone();
                $('#optionList').append($clone);
                $clone.removeClass('hide option-row');
                $clone.find('.remove-option').on('click', function(e) {
                    e.preventDefault();
                    removeOptionRow($(this));
                })
                $clone.find('.option-select').selectize();
            }

            function removeOptionRow($trigger) {
                $trigger.parent().parent().parent().remove();
            }
        });
    </script>
@endsection
