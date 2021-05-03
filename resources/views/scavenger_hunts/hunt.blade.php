@extends('layouts.app')

@section('title') Scavenger Hunt :: {{ $hunt->displayName }} @endsection

@section('content')
{!! breadcrumbs(['Scavenger Hunts' => $hunt->url, $hunt->displayName => $hunt->url]) !!}

<div class="row">
    <div class="col-sm">
    </div>
    <div class="col-lg-8 col-md-12">
        <div class="mb-3">
            
            <h2>{{ $hunt->displayName }}</h2>
            
            <div class="text-center">
                @if(isset($participantLog))
                    @foreach($logArray as $key => $found)
                        @if(isset($found))
                            <span class="px-2 mb-2">
                                {!! $hunt->targets[$key - 1]->displayItemShort !!}
                            </span>
                        @endif
                    @endforeach
                @endif

                <p>
                    You have found 
                    @if(isset($participantLog))
                        {{$participantLog->targetsCount}}
                    @else
                        0
                    @endif
                    /{{ count($hunt->targets) }} targets!
                </p>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Information</h4>
                </div>
                <div class="card-body">
                <div><strong>Start Time: </strong>{!! format_date($hunt->start_at) !!} ({{ $hunt->start_at->diffForHumans() }})</div>
                <div class="mb-2"><strong>End Time: </strong>{!! format_date($hunt->end_at) !!} ({{ $hunt->end_at->diffForHumans() }})</div>
                
                @if($hunt->summary)
                    <i>{{ $hunt->summary }}</i>
                @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if($hunt->clue)
                        <p>Here's a clue to get you started...</p>
                            <i>{{ $hunt->clue }}</i>
                    @else
                        <p>There doesn't seem to be a clue for this hunt. You're on your own!</p>
                    @endif

                    <hr/>

                    @if(isset($participantLog))
                        @foreach($logArray as $key => $found)
                            @if(isset($found))
                                @if(isset($hunt->targets[$key - 1]->description))
                                    <p>The <strong>{!! $hunt->targets[$key - 1]->item->name !!}</strong> had this message for you:</p>
                                    <p>
                                        <i>{!! $hunt->targets[$key - 1]->description !!}</i>
                                    </p>
                                @endif
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>
    <div class="col-sm">
    </div>
</div>

@endsection
