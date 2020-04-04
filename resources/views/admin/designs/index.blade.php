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
<table class="table table-sm">
    <thead>
        <tr>
            <th>{{ $isMyo ? 'MYO Slot' : 'Character' }}</th>
            <th>User</th>
            <th width="20%">Submitted</th>
            <th width="20%">Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($requests as $r)
            <tr>
                <td>{!! $r->character ? $r->character->displayName : 'Deleted Character [#'.$r->character_id.']' !!}</td>
                <td>{!! $r->user->displayName !!}</td>
                <td>{!! $r->submitted_at ? format_date($r->submitted_at) : '---' !!}</td>
                <td>
                    <span class="badge badge-{{ $r->status == 'Pending' ? 'secondary' : ($r->status == 'Approved' ? 'success' : 'danger') }}">{{ $r->status }}</span>
                </td>
                <td class="text-right"><a href="{{ $r->url }}" class="btn btn-primary btn-sm">Details</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $requests->render() !!}


@endsection