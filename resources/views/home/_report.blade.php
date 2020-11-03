<tr>
    <td class="text-break">@if(!$report->is_br)<a href="{{ $report->url }}">@endif {{ $report->url }} @if(!$report->is_br)</a>@endif</td>
    <td>{!! format_date($report->created_at) !!}</td>
    <td>
        <span class="badge badge-{{ $report->status == 'Pending' ? 'secondary' : ($report->status == 'Closed' ? 'success' : 'danger') }}">{{ $report->status }}</span>
    </td>
    @if($report->status == 'Closed' || ($report->status == 'Assigned' && $report->is_br && $report->error_type != 'exploit') || (Auth::check() && Auth::user()->id == $report->user_id)) 
        <td class="text-right"><a href="{{ $report->viewUrl }}" class="btn btn-primary btn-sm">Details</a></td>
    @else 
        <td class="text-right"><div class="btn btn-dark btn-sm">Report not closed</a></td>
    @endif
</tr>