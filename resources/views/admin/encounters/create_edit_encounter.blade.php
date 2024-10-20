@extends('admin.layout')

@section('admin-title')
    Encounters
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Encounters' => 'admin/data/encounters',
        ($encounter->id ? 'Edit' : 'Create') . ' Encounter' => $encounter->id ? 'admin/data/encounters/edit/' . $encounter->id : 'admin/data/encounters/create',
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
                {!! Form::label('Encounter Image (Optional)') !!} {!! add_help('This image will show up when the user gets this encounter.') !!}
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
    <h5>Image Positioning</h5>
    <p>Where the image will rest on the encounter page (relative to the area background)</p>
    <div class="row">
        <div class="form-group col-6">
            {!! Form::label('position_right', 'Right Position') !!} {!! add_help('The positioning as seen from the right. As this is a percentage, it should be a number 1-100. Please note that images set to 100 will fall of the container, and can not be seen.') !!}
            {!! Form::number('position_right', isset($encounter->extras['position_right']) ? $encounter->extras['position_right'] : '', ['class' => 'form-control', 'placeholder' => 'Right Position', 'min' => 1, 'max' => 100]) !!}
        </div>
        <div class="form-group col-6">
            {!! Form::label('position_bottom', 'Bottom Position') !!} {!! add_help('The positioning as seen from the bottom. As this is a percentage, it should be a number 1-100. Please note that images set to 100 will fall of the container, and can not be seen.') !!}
            {!! Form::number('position_bottom', isset($encounter->extras['position_bottom']) ? $encounter->extras['position_bottom'] : '', ['class' => 'form-control', 'placeholder' => 'Bottom Position', 'min' => 1, 'max' => 100]) !!}
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
        {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('encounters that are not active will be hidden from the encounter list. They also cannot be automatically set as the next active encounter.') !!}
    </div>


    <div class="text-right">
        {!! Form::submit($encounter->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($encounter->id)
        <div class="card-body text-center">
            <h4>Encounter Options</h4>
            <p>You can create a series of options the user can choose from when they run into this encounter. You can
                create a short
                option that can be clicked on, as well as a description of the result from the result of that option.
            </p>
            <p>You can also choose if the user gets rewards or not, they will get the rewards that you choose for this
                encounter
                below.</p>
            <div class="mb-2 text-right">
                <a href="#" class="btn btn-primary" id="add-prompt">Add Option</a>
            </div>
        </div>
        @foreach ($encounter->prompts as $prompt)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
                <div class="col-5 text-truncate">
                    {{ $prompt->name }}
                    <i class="fas fa-bell mr-2 {{ $prompt->extras['result_type'] == 'success' ? 'text-success' : ($prompt->extras['result_type'] == 'failure' ? 'text-danger' : '') }}" data-toggle="tooltip"
                        title="{{ $prompt->extras['result_type'] == 'success' ? 'Success Alert' : ($prompt->extras['result_type'] == 'failure' ? 'Fail Alert' : 'Neutral Alert') }}"></i>
                    @if ($prompt->limits->count())
                        <i class="fas fa-lock mr-2" data-toggle="tooltip" title="Has limits"></i>
                    @endif
                    @if ($prompt->rewards)
                        <i class="fas fa-gift mr-2" data-toggle="tooltip" title="Has reward"></i>
                    @endif
                    @if ($prompt->extras != null && $prompt->extras['math_type'] != null && $prompt->extras['energy_value'] != null)
                        @if ($prompt->extras['math_type'] == 'subtract')
                            <i class="fas fa-bolt text-warning mr-2" data-toggle="tooltip" title="Removes {{ $prompt->extras['energy_value'] }} Energy"></i>
                        @else
                            <i class="fas fa-heart text-success mr-2" data-toggle="tooltip" title="Restores {{ $prompt->extras['energy_value'] }} Energy"></i>
                        @endif
                    @endif
                </div>
                <div class="col-3 col-md-1 text-right">
                    <a href="#" class="btn btn-sm btn-primary edit-prompt" data-id="{{ $prompt->id }}"><i class="fas fa-cog mr-1"></i>Edit</a>
                </div>
            </div>
        @endforeach
    @endif

@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function() {
            $('.delete-encounter-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/encounters/delete') }}/{{ $encounter->id }}",
                    'Delete Encounter');
            });

            $('#add-prompt').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/encounters/edit/' . $encounter->id . '/prompts/create') }}",
                    'Create Prompt');
            });

            $('.edit-prompt').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/encounters/edit/' . $encounter->id . '/prompts/edit') }}/" +
                    $(this).data('id'), 'Edit Prompt');
            });

        });
    </script>
@endsection
