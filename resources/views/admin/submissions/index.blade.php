@extends('admin.layout')

@section('admin-title') {{ $isClaims ? 'Claim' : 'Prompt' }} Queue @endsection

@section('admin-content')
@if($isClaims)
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Claim Queue' => 'admin/claims/pending']) !!}
@else
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Prompt Queue' => 'admin/submissions/pending']) !!}
@endif

<h1>
    {{ $isClaims ? 'Claim' : 'Prompt' }} Queue
</h1>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isClaims ? 'claims' : 'submissions').'/pending*') }} {{ set_active('admin/'.($isClaims ? 'claims' : 'submissions')) }}" href="{{ url('admin/'.($isClaims ? 'claims' : 'submissions').'/pending') }}">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isClaims ? 'claims' : 'submissions').'/approved*') }}" href="{{ url('admin/'.($isClaims ? 'claims' : 'submissions').'/approved') }}">Approved</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/'.($isClaims ? 'claims' : 'submissions').'/rejected*') }}" href="{{ url('admin/'.($isClaims ? 'claims' : 'submissions').'/rejected') }}">Rejected</a>
  </li>
</ul>

{!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
    <div class="form-inline justify-content-end">
        @if(!$isClaims)
            <div class="form-group ml-3 mb-3">
                {!! Form::select('prompt_category_id', $categories, Request::get('prompt_category_id'), ['class' => 'form-control']) !!}
            </div>
        @endif
    </div>
    <div class="form-inline justify-content-end">
        <div class="form-group ml-3 mb-3">
            {!! Form::select('sort', [
                'newest'         => 'Newest First',
                'oldest'         => 'Oldest First',
            ], Request::get('sort') ? : 'oldest', ['class' => 'form-control']) !!}
        </div>
        <div class="form-group ml-3 mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
{!! Form::close() !!}

{!! $submissions->render() !!}
<table>
    <thead>
        <table class="table table-sm">
            <thead>
                <tr>
                    @if(!$isClaims)
                        <th width="30%">Prompt</th>
                    @endif
                    <th>User</th>
                    <th width="20%">Link</th>
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
                        <td>{!! $submission->user->displayName !!}</td>
                        <td class="text-break"><a href="{{ $submission->url }}">{{ $submission->url }}</a></td>
                        <td>{!! format_date($submission->created_at) !!}</td>
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