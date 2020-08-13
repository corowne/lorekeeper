@extends('admin.layout')

@section('admin-title') {{ $isMyo ? 'MYO Approval' : 'Design Update' }} Queue @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', ($isMyo ? 'MYO Approval' : 'Design Update').' Queue' => 'admin/designs/pending']) !!}

<h1>
    {{ $isMyo ? 'MYO Approval' : 'Design Update' }} Queue
</h1>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isMyo ? 'myo-approvals' : 'design-approvals').'/pending*') }}" href="{{ url('admin/'.($isMyo ? 'myo-approvals' : 'design-approvals').'/pending') }}">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isMyo ? 'myo-approvals' : 'design-approvals').'/approved*') }}" href="{{ url('admin/'.($isMyo ? 'myo-approvals' : 'design-approvals').'/approved') }}">Approved</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isMyo ? 'myo-approvals' : 'design-approvals').'/rejected*') }}" href="{{ url('admin/'.($isMyo ? 'myo-approvals' : 'design-approvals').'/rejected') }}">Rejected</a>
  </li>
</ul>

{!! $requests->render() !!}
  <div class="row ml-md-2">
    <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
      <div class="col-md-3 font-weight-bold">{{ $isMyo ? 'MYO Slot' : 'Character' }}</div>
      <div class="col-3 col-md-3 font-weight-bold">User</div>
      <div class="col-2 col-md-2 font-weight-bold">Submitted</div>
      <div class="col-2 col-md-2 font-weight-bold">Votes</div>
      <div class="col-4 col-md-2 font-weight-bold">Status</div>
    </div>
    @foreach($requests as $r)
    <?php
        $rejectSum = 0;
        $approveSum = 0;
        foreach($r->voteData as $voter=>$vote) {
            if($vote == 1) $rejectSum += 1;
            if($vote == 2) $approveSum += 1;
        }
    ?>
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-md-3">{!! $r->character ? $r->character->displayName : 'Deleted Character [#'.$r->character_id.']' !!}</div>
      <div class="col-3 col-md-3">{!! $r->user->displayName !!}</div>
      <div class="col-2 col-md-2">{!! $r->submitted_at ? pretty_date($r->submitted_at) : '---' !!}</div>
      <div class="col-2 col-md-2"><strong>
        <span class="text-danger">{{ $rejectSum }}/{{ Settings::get('design_votes_needed') }}</span> :
        <span class="text-success">{{ $approveSum }}/{{ Settings::get('design_votes_needed') }}</span>
        </strong></div>
      <div class="col-4 col-md-1"><span class="btn btn-{{ $r->status == 'Pending' ? 'secondary' : ($r->status == 'Approved' ? 'success' : 'danger') }} btn-sm py-0 px-1">{{ $r->status }}</span></div>
      <div class="col-4 col-md-1"><a href="{{ $r->url }}" class="btn btn-primary btn-sm">Details</a></div>
    </div>
    @endforeach
  </div>
{!! $requests->render() !!}

<div class="text-center mt-4 small text-muted">{{ $requests->total() }} result{{ $requests->total() == 1 ? '' : 's' }} found.</div>

@endsection
