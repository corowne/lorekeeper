<tr>
    <td class="text-break"><a href="{{ $report->url }}">{{ $report->url }}</a></td>
    <td>{!! format_date($report->created_at) !!}</td>
    <td>
        <span class="badge badge-{{ $report->status == 'Pending' ? 'secondary' : ($report->status == 'Closed' ? 'success' : 'danger') }}">{{ $report->status }}</span>
    </td>
    <td class="text-right"><a href="{{ $report->viewUrl }}" class="btn btn-primary btn-sm">Details</a></td>
</tr>