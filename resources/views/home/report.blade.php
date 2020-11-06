@extends('user.layout')

@section('profile-title') Report (#{{ $report->id }}) @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Report (#' . $report->id . ')' => $report->viewUrl]) !!}

@if(Auth::user()->id == $report->user->id || Auth::user()->hasPower('manage_reports') || ($report->is_br == 1 && ($report->status == 'Closed' || $report->error_type != 'exploit')))
@include('home._report_content', ['report' => $report]) 
@else
<div class="alert alert-danger">Reports are private. Please contact support if you believe this is a mistake.</div>
@endif

@endsection