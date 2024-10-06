@extends('character.design.layout')

@section('design-title') Design Approval Request (#{{ $request->id }}) :: Comments @endsection

@section('design-content')
{!! breadcrumbs(['Design Approvals' => 'designs', 'Request (#' . $request->id . ')' => 'designs/' . $request->id, 'Traits' => 'designs/' . $request->id . '/traits']) !!}

@include('character.design._header', ['request' => $request])

@if($request->status == 'Pending' && Auth::check() && Auth::user()->hasPower('manage_characters'))
    {!! Form::open(['url' => 'designs/'.$request->id.'/traits']) !!}

        <div class="form-group">
            {!! Form::label('species_id', 'Species') !!}
            {!! Form::select('species_id', $specieses, $request->species_id, ['class' => 'form-control', 'id' => 'species']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('secondary_species_id', 'Secondary species') !!}
            {!! Form::select('secondary_species_id', $specieses, $request->secondary_species_id, ['class' => 'form-control', 'id' => 'secondary_species']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('subtype_id', 'Species Subtype') !!}
            <div id="subtypes">
                {!! Form::select('subtype_id', $subtypes, $request->subtype_id, ['class' => 'form-control', 'id' => 'subtype']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('rarity_id', 'Character Rarity') !!}
            {!! Form::select('rarity_id', $rarities, $request->rarity_id, ['class' => 'form-control', 'id' => 'rarity']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Traits') !!}
            <div id="featureList">
                {{-- Add in the compulsory traits for MYO slots --}}
                @if($request->character->is_myo_slot && $request->character->image->features)
                    @foreach($request->character->image->features as $feature)
                        <div class="mb-2 d-flex align-items-center">
                            {!! Form::text('', $feature->name, ['class' => 'form-control mr-2']) !!}
                            {!! Form::text('', $feature->data, ['class' => 'form-control mr-2']) !!}
                            <div>{!! add_help('This trait is required.') !!}</div>
                        </div>
                    @endforeach
                @endif

                {{-- Add in the ones that currently exist --}}
                @if($request->features)
                    @foreach($request->features as $feature)
                        <div class="mb-2 d-flex">
                            {!! Form::select('feature_id[]', $features, $feature->feature_id, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Select Trait']) !!}
                            {!! Form::text('feature_data[]', $feature->data, ['class' => 'form-control mr-2', 'placeholder' => 'Extra Info (Optional)']) !!}
                            @if(false)
                                <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
            @if(false)
                <div><a href="#" class="btn btn-primary" id="add-feature">Add Trait</a></div>
                <div class="feature-row hide mb-2">
                    {!! Form::select('feature_id[]', $features, null, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Select Trait']) !!}
                    {!! Form::text('feature_data[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Extra Info (Optional)']) !!}
                    @if(false)
                        <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
                    @endif
                </div>
            @endif
        </div>

        <div class="text-right">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
@else
    <div class="mb-1">
        <div class="row">
            <div class="col-md-2 col-4"><h5>Species</h5></div>
            <div class="col-md-10 col-8">{!! $request->species ? $request->species->displayName : 'None' !!}</div>
        </div>
        @if($request->secondarySpecies != null)
            <div class="row">
                <div class="col-md-2 col-4"><h5>Secondary species</h5></div>
                <div class="col-md-10 col-8">{!! $request->secondarySpecies ? $request->secondarySpecies->displayName : 'None' !!}</div>
            </div>
        @endif
        @if($request->subtype_id)
        <div class="row">
            <div class="col-md-2 col-4"><h5>Subtype</h5></div>
            <div class="col-md-10 col-8">
            @if($request->character->is_myo_slot && $request->character->image->subtype_id)
                {!! $request->character->image->subtype->displayName !!}
            @else
                {!! $request->subtype_id ? $request->subtype->displayName : 'None Selected' !!}
            @endif
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-2 col-4"><h5>Rarity</h5></div>
            <div class="col-md-10 col-8">{!! $request->rarity ? $request->rarity->displayName : 'None Selected' !!}</div>
        </div>
    </div>
    <h5>Traits</h5>
    <div>
        @if($request->character && $request->character->is_myo_slot && $request->character->image->features)
            @foreach($request->character->image->features as $feature)
                <div>@if($feature->feature->feature_category_id) <strong>[{!! $feature->feature->category->displayName !!}]</strong> @endif {!! $feature->feature->displayName !!} @if($feature->data) ({{ $feature->data }}) @endif</div>
            @endforeach
        @endif
        @foreach($request->features as $feature)
            <div>@if($feature->feature->feature_category_id) <strong>[{!! $feature->feature->category->displayName !!}]</strong> @endif {!! $feature->feature->displayName !!} @if($feature->data) ({{ $feature->data }}) @endif</div>
        @endforeach
    </div>
@endif

@endsection

@section('scripts')
@include('widgets._image_upload_js')

<script>
  $( "#species" ).change(function() {
    var species = $('#species').val();
    var id = '<?php echo($request->id); ?>';
    $.ajax({
      type: "GET", url: "{{ url('designs/traits/subtype') }}?species="+species+"&id="+id, dataType: "text"
    }).done(function (res) { $("#subtypes").html(res); }).fail(function (jqXHR, textStatus, errorThrown) { alert("AJAX call failed: " + textStatus + ", " + errorThrown); });

  });
</script>

@endsection
