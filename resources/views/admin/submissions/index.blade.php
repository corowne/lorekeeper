@extends('admin.layout')

@section('admin-title') {{ $isClaims ? 'Claim' : 'Prompt' }} Queue @endsection

@section('admin-content')
@if($isClaims)
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Claim Queue' => 'admin/claims/pending']) !!}
@else
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Prompt Queue' => 'admin/submissions/pending']) !!}
@endif

<h1>
    Claim Queue
</h1>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isClaims ? 'claims' : 'submissions').'/pending*') }}" href="{{ url('admin/'.($isClaims ? 'claims' : 'submissions').'/pending') }}">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isClaims ? 'claims' : 'submissions').'/approved*') }}" href="{{ url('admin/'.($isClaims ? 'claims' : 'submissions').'/approved') }}">Approved</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isClaims ? 'claims' : 'submissions').'/rejected*') }}" href="{{ url('admin/'.($isClaims ? 'claims' : 'submissions').'/rejected') }}">Rejected</a>
  </li>
</ul>

{!! $submissions->render() !!}
<table>
    <thead>
        <table class="table table-sm">
            <thead>
                <tr>
                    @if(!$isClaims)
                        <th width="30%">Prompt</th>
                    @endif
                    <th width="30%">Link</th>
                    <th width="20%">Submitted</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                    <tr>
                        @if(!$isClaims)
                            <td>{!! $submission->prompt->displayName !!}</td>
                        @endif
                        <td><a href="{{ $submission->url }}">{{ $submission->url }}</a></td>
                        <td>{{ format_date($submission->created_at) }}</td>
                        <td>
                            <span class="badge badge-{{ $submission->status == 'Pending' ? 'secondary' : ($submission->status == 'Approved' ? 'success' : 'danger') }}">{{ $submission->status }}</span>
                        </td>
                        <td class="text-right"><a href="{{ $submission->adminUrl }}" class="btn btn-primary btn-sm">Details</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </thead>
</table>
{!! $submissions->render() !!}


@endsection