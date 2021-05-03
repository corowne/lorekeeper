@extends('layouts.app')

@section('title') Scavenger Hunt :: {{ $target->item->name }} @endsection

@section('content')
{!! breadcrumbs(['Scavenger Hunts' => $hunt->url, $hunt->displayName => $hunt->url, $target->item->name => $target->url]) !!}

<div class="row">
    <div class="col-sm">
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="mb-3 pt-3 text-center card">
            <div class="card-body">
                <p>
                    {!! $target->displayItemLong !!}
                </p>

                <p>
                    Congratulations! Click the button below to log that you've found this target and claim this prize.
                </p>

                <div class="mb-4">
                    @if($target->description)
                    <div class="text-left">
                            <p>
                                It seems a message was attached!
                            </p>
                            <p>
                                <i>{{ $target->description }}</i>
                            </p>
                    </div>
                    @endif

                    @if($hunt->isActive)
                        @if(!isset($participantLog[$target->targetField]))
                            {!! Form::open(['url' => 'hunts/targets/claim']) !!}
                                {!! Form::hidden('page_id', $target->page_id) !!}
                                {!! Form::submit('Claim', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        @else
                            <p><strong>You've already claimed this!</strong></p>
                        @endif
                    @else
                        <p>This hunt isn't active. Check the <a href="{{ $hunt->url }}">hunt's page</a> for more information!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm">
    </div>
</div>

@endsection
