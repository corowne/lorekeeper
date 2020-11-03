<tr>
    <td class="text-break"><a href="{{ $report->viewUrlurl }}">{{ $report->viewUrl }}</a></td>
    <td>{!! format_date($report->created_at) !!}</td>
    <td>
        <span class="badge badge-{{ $report->status == 'Pending' ? 'secondary' : ($report->status == 'Closed' ? 'success' : 'danger') }}">{{ $report->status }}</span>
    </td>
    @if($report->status == 'Closed') <td class="text-right"><a href="{{ $report->viewUrl }}" class="btn btn-primary btn-sm">Details</a></td>
    @else <td class="text-right"><a href="{{ $report->viewUrl }}" class="btn btn-dark btn-sm">Report not closed</a></td>
    @endif
</tr>