@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->is_myo_slot ? 'MYO Approval' : 'Design Update' }} for {{ $character->fullName }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, ($character->is_myo_slot ? 'MYO Approval' : 'Design Update') => $character->url.'/approval']) !!}

@include('character._header', ['character' => $character])

<h3>
    {{ $character->is_myo_slot ? 'MYO Approval' : 'Design Update' }} Request
</h3>
@if(!$queueOpen)
    <div class="alert alert-danger">
        The {{ $character->is_myo_slot ? 'MYO approval' : 'design update' }} queue is currently closed. You cannot submit a new approval request at this time.
    </div>
@elseif(!$request)
    <p>No {{ $character->is_myo_slot ? 'MYO approval' : 'design update' }} request found. Would you like to create one?</p>
    <p>This will prepare a request to approve {{ $character->is_myo_slot ? 'your MYO slot\'s design' : 'a design update for your character' }}, which will allow you to upload a new masterlist image, list their new traits and spend items/currency on the design. You will be able to edit the contents of your request as much as you like before submission. Staff will be able to view the draft and provide feedback. </p>
    {!! Form::open(['url' => $character->is_myo_slot ? 'myo/'.$character->id.'/approval' : 'character/'.$character->slug.'/approval']) !!}
    <div class="text-right">
        {!! Form::submit('Create Request', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
@else
    <p>You have a {{ $character->is_myo_slot ? 'MYO approval' : 'design update' }} request {{ $request->status == 'Draft' ? 'that has not been submitted' : 'awaiting approval' }}. <a href="{{ $request->url }}">Click here to view {{ $request->status == 'Draft' ? 'and edit ' : '' }}it.</a></p>
@endif 
{{--
@else
    <p>This form is for submitting this {{ $character->is_myo_slot ? 'MYO slot' : 'character' }} to the {{ $character->is_myo_slot ? 'MYO approval' : 'design update' }} queue. Keeping in mind the allowed traits/stats for your update request, select the desired traits/stats below. You may also select currency and items to spend on this approval - these will be deducted from your account immediately, but refunded to you in case of a rejection.</p>
    {!! Form::open(['url' => $character->is_myo_slot ? 'myo/'.$character->id.'/approval' : 'character/'.$character->slug.'/approval']) !!}
    <div class="form-group">
        {!! Form::label('comments', 'Comments (Optional)') !!} {!! add_help('Enter a comment that will be added onto your '.($character->is_myo_slot ? 'MYO approval' : 'design update').' request - suggestions would be to include calculations or how you intend to use attached items or currency if applicable. Staff will read this comment while reviewing your request.') !!}
        {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
    </div>

    <h3>Image Upload</h3>

    

        <h3>Add-ons</h3>
        <p>You can select items from your inventory and/or currencies from your bank{{ $character->is_myo_slot ? '' : ' or your character\'s bank' }} to attach to this request. Note that this will consume the items/currency, but will be refunded if the request is rejected. This is entirely optional; please follow any restrictions set by staff regarding restrictions on what you may add to a request.</p>

        <h3>Traits</h3>
    
        <div class="text-right">
            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
@endif
--}}
@endsection

@section('scripts')
@include('widgets._image_upload_js')
<script>
    $(document).ready(function(){
    });
</script>
@endsection