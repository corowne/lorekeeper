@extends('character.design.layout')

@section('design-title') Design Approval Request (#{{ $request->id }}) @endsection

@section('design-content')
{!! breadcrumbs(['Design Approvals' => 'designs', 'Request (#' . $request->id . ')' => 'designs/' . $request->id]) !!}

@include('character.design._header', ['request' => $request])

@if($request->status == 'Draft')
    <p>
        This request has not been submitted to the approval queue yet. 
        @if($request->user_id == Auth::user()->id)
        Staff members are able to view this page when provided with a direct link. Click on any of the tabs to edit the section.
        @else 
            As a staff member with the ability to edit the masterlist, you can view the details of the request by clicking the tabs.
        @endif
    </p>
    @if($request->user_id == Auth::user()->id)
        @if($request->isComplete)
            {!! Form::open(['url' => 'designs/'.$request.'/submit', 'id' => 'submitForm', 'class' => 'text-right']) !!}
                {!! Form::submit('Submit Request', ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        @else
            <p class="text-danger">Not all sections have been completed yet. Please visit the necessary tab(s) and click Save to update them, even if no modifications to the information are needed.</p>
            <div class="text-right">
                <button class="btn btn-primary" disabled>Submit Request</button>
            </div>
        @endif
    @endif
@elseif($request->status == 'Pending')
    <p>
        This request is in the approval queue. 
        @if($request->user_id == Auth::user()->id)
            Please wait for it to be processed.
        @else 
            As a staff member with the ability to edit the masterlist, you can view the details of the request, but cannot edit the contents directly. 
            <br /><strong class="text-secondary">Cancelling</strong> the request returns it to its draft status, allowing the user to make further edits. 
            <br /><strong class="text-success">Approving</strong> the request creates the update. 
            <br /><strong class="text-danger">Rejecting</strong> the update returns any attached items and the user may not edit it any more.
        @endif
    </p>
@elseif($request->status == 'Approved')
    <p>This request has been approved. The data is preserved as a record of this submission.</p>
@else
    <p>This request was rejected by {!! $request->staff->displayName !!}.</p>
    @if($request->staff_comments && ($request->user_id == Auth::user()->id || Auth::user()->hasPower('manage_submissions')))
        <h5 class="text-danger">Staff Comments</h5>
        <div class="card border-danger mb-3"><div class="card-body">{!! nl2br(htmlentities($request->staff_comments)) !!}</div></div>
    @else
        <p>No rejection comment was provided.</p>
    @endif
@endif

@endsection