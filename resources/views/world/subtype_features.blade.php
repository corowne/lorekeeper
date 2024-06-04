@extends('world.layout')

@section('world-title')
    {{ $subtype->name }} ({!! $subtype->species->name !!} Subtype) Traits
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Subtypes' => 'world/subtypes', $subtype->name => $subtype->url, 'Traits' => 'world/subtypes/' . $subtype->id . 'traits']) !!}
    <h1>{{ $subtype->name }} ({!! $subtype->species->name !!} Subtype) Traits</h1>

    <p>This is a visual index of all {!! $subtype->displayName !!} ({!! $subtype->species->displayName !!} Subtype)-specific traits. Click a trait to view more info on it!</p>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::checkbox('add_basics', 1, Request::get('add_basics'), ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('add_basics', 'Add Species traits', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is on, other traits from the species that lack a subtype will be added to the page.') !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Refresh', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    @foreach ($features as $categoryId => $categoryFeatures)
        @if (!isset($categories[$categoryId]) || (Auth::check() && Auth::user()->hasPower('edit_data')) || $categories[$categoryId]->is_visible)
            <div class="card mb-3 inventory-category">
                <h5 class="card-header inventory-header">
                    @if (isset($categories[$categoryId]) && !$categories[$categoryId]->is_visible)
                        <i class="fas fa-eye-slash mr-1"></i>
                    @endif
                    {!! isset($categories[$categoryId]) ? '<a href="' . $categories[$categoryId]->searchUrl . '">' . $categories[$categoryId]->name . '</a>' : 'Miscellaneous' !!}
                </h5>
                <div class="card-body inventory-body">
                    @foreach ($categoryFeatures->chunk(4) as $chunk)
                        <div class="row mb-3">
                            @foreach ($chunk as $featureId => $feature)
                                <div class="col-sm-3 col-6 text-center align-self-center inventory-item">
                                    @if ($feature->first()->has_image)
                                        <a class="badge" style="border-radius:.5em; {{ $feature->first()->rarity->color ? 'background-color:#' . $feature->first()->rarity->color : '' }}" href="{{ $feature->first()->url }}">
                                            <img class="my-1 modal-image" style="max-height:100%; height:150px; border-radius:.5em;" src="{{ $feature->first()->imageUrl }}" alt="{{ $feature->first()->name }}" data-id="{{ $feature->first()->id }}" />
                                        </a>
                                    @endif
                                    <p>
                                        @if (!$feature->first()->is_visible)
                                            <i class="fas fa-eye-slash mr-1"></i>
                                        @endif
                                        {!! $feature->first()->displayName !!}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
@endsection

@section('scripts')
    @if (config('lorekeeper.extensions.visual_trait_index.trait_modals'))
        <script>
            $(document).ready(function() {
                $('.modal-image').on('click', function(e) {
                    e.preventDefault();

                    loadModal("{{ url('world/traits/modal') }}/" + $(this).data('id'), 'Trait Detail');
                });
            })
        </script>
    @endif
@endsection
