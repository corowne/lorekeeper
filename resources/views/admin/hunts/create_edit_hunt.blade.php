@extends('admin.layout')

@section('admin-title') {{ $hunt->id ? 'Edit' : 'Create' }} Scavenger Hunt @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Scavenger Hunts' => 'admin/data/hunts', ($hunt->id ? 'Edit' : 'Create').' Scavenger Hunt' => $hunt->id ? 'admin/data/hunts/edit/'.$hunt->id : 'admin/data/hunts/create']) !!}

<h1>{{ $hunt->id ? 'Edit' : 'Create' }} Scavenger Hunt
    @if($hunt->id)
        <a href="#" class="btn btn-danger float-right delete-hunt-button">Delete Hunt</a>
    @endif
</h1>

{!! Form::open(['url' => $hunt->id ? 'admin/data/hunts/edit/'.$hunt->id : 'admin/data/hunts/create']) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!} {!! add_help('This is the name you will use to identify this hunt internally. This name will not be shown to users; a name that can be easily identified is recommended.') !!}
    {!! Form::text('name', $hunt->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Display Name') !!} {!! add_help('This is the name that will be shown to users. This is for display purposes and can be something more vague than the above.') !!}
    {!! Form::text('display_name', $hunt->getOriginal('display_name'), ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Summary (Optional)') !!} {!! add_help('This is a short blurb that shows up when viewing a hunt\'s page. HTML cannot be used here.') !!}
    {!! Form::text('summary', $hunt->summary, ['class' => 'form-control', 'maxLength' => 250]) !!}
</div>

<div class="form-group">
    {!! Form::label('Clue (Optional)') !!} {!! add_help('You can provide an initial clue to direct users here.') !!}
    {!! Form::text('clue', $hunt->clue, ['class' => 'form-control', 'maxLength' => 250]) !!}
</div>

@if($hunt->id)
    <h4>Locations <a class="small inventory-collapse-toggle collapse-toggle" href="#spoilers" data-toggle="collapse">Show</a></h3>
    <div class="mb-3 collapse form-group" id="spoilers">
        {!! Form::label('locations', 'Locations (Optional)') !!} {!! add_help('The locations of hunt targets. HTML cannot be used.') !!}
        {!! Form::textarea('locations', $hunt->locations, ['class' => 'form-control']) !!}
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('start_at', 'Start Time') !!} {!! add_help('Hunts can be viewed before the starting time, but targets cannot be claimed.') !!}
            {!! Form::text('start_at', $hunt->start_at, ['class' => 'form-control datepicker']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('end_at', 'End Time') !!} {!! add_help('Hunts can be viewed after the ending time, but targets cannot be claimed.') !!}
            {!! Form::text('end_at', $hunt->end_at, ['class' => 'form-control datepicker']) !!}
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::submit($hunt->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($hunt->id)
    <h3>Display Link</h3>
    <p>For convenience, here is the hunt's url as well as the full HTML to display a link to the hunt's user-facing page. Hunt's pages inform users of how many of the hunt's targets they've found, and provide a recap of any clues attached to the found targets. Targets not yet found are not displayed, aside from indicating the total number; the full lineup can thus be indicated (or not) as desired.</p>
    <div class="alert alert-secondary">
        {{ $hunt->url }}
    </div>
    <div class="alert alert-secondary">
        {{ $hunt->displayLink }}
    </div>
@endif

@if($hunt->id)
    <h3>Hunt Targets</h3>
    <p>Hunt targets are items with a specified quantity. They are granted to the user on being claimed, and can only be claimed once. Each target is assigned a number, 1-10, per hunt, based on the order they are added to the hunt. Targets can be deleted so long as the hunt has not had any participants, as doing so after would break the logs. Users will also be shown the number of targets they have found out of the total, so make sure you have only the number of targets desired before the hunt goes live!</p>

    @if(count($hunt->targets) < 10)
    <div class="text-right">
        <a href="{{ url('admin/data/hunts/targets/create/'.$hunt->id) }}" class="btn btn-outline-primary">Add a Target</a>
    </div>
    @endif

    @if(count($hunt->targets))
    <div class="row ml-md-2 mb-3">
      <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
        <div class="col-md-2 font-weight-bold text-center">Target Number</div>
        <div class="col-md font-weight-bold">Item</div>
      </div>
      @foreach($hunt->targets as $target)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-md-2 text-center">
            {{ $target->targetNumber }}
            </div>
            <div class="col-md text-truncate">
            {!! $target->displayItem !!}
            </div>
            <div class="col-3 col-md-1 text-right">
            <a href="{{ url('admin/data/hunts/targets/edit/'.$target->id) }}"  class="btn btn-primary py-0 px-2">Edit</a>
            </div>
      </div>
      @endforeach
    </div>
    @else 
        <p>This hunt has no targets yet.</p> 
    @endif
@endif

@if($hunt->id)
    <h3>Log</h3>
    <p>
        This is the log of claimed targets. It's organized per user, and claimed targets are represented by a checkmark with the timestamp in the adjacent tooltip.
    </p>

    @if(count($hunt->participants))
    {!! $participants->render() !!}

    <div class="row ml-md-2 mb-3">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-md-2 font-weight-bold">User</div>
            @foreach($hunt->targets as $target)
                <div class="col-md font-weight-bold text-center">Target {{ $target->targetNumber }}</div>
            @endforeach
        </div>
        @foreach($participants as $participant)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-md-2">
            {!! $participant->user->displayName !!}
            </div>
            @foreach($hunt->targets as $target)
                <div class="col-md text-center">
                    @if(isset($participant[$target->targetField]))
                        <i class="text-success fas fa-check"></i> {!! add_help($participant[$target->targetField]) !!}
                    @endif
                </div>
            @endforeach
        </div>
        @endforeach
    </div>

    {!! $participants->render() !!}
    @else
        <p>No participants found!</p>
    @endif

@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-hunt-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/hunts/delete') }}/{{ $hunt->id }}", 'Delete Hunt');
    });
    
    $( ".datepicker" ).datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: 'HH:mm:ss',
    });
});
    
</script>
@endsection