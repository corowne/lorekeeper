<div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
    <div class="col-6 col-md-4">
    <span class="ubt-texthide">@if(!$report->is_br)<a href="{{ $report->url }}">@endif {{ $report->url }} @if(!$report->is_br)</a>@endif</span>
    </div>
    <div class="col-6 col-md-5">{!! pretty_date($report->created_at) !!}</div>
    <div class="col-6 col-md-1">
        <span class="badge badge-{{ $report->status == 'Pending' ? 'secondary' : ($report->status == 'Closed' ? 'success' : 'danger') }}">{{ $report->status }}</span>
    </div>
    <div class="col-6 col-md-2 text-right">
        @if($report->status == 'Closed' || ($report->status == 'Assigned' && $report->is_br && $report->error_type != 'exploit') || (Auth::check() && Auth::user()->id == $report->user_id)) 
            <td class="text-right"><a href="{{ $report->viewUrl }}" class="btn btn-primary btn-sm">Details</a></td>
        @else 
            <td class="text-right"><div class="btn btn-dark btn-sm">Report not closed</a></td>
        @endif
    </div>
</div>