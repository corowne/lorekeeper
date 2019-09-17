<h1>
    Request (#{{ $request->id }}): {!! $request->character->displayName !!}
    <span class="float-right badge badge-{{ ($request->status == 'Draft' || $request->status == 'Pending') ? 'secondary' : ($request->status == 'Approved' ? 'success' : 'danger') }}">{{ $request->status }}
</h1>

@if(isset($request->staff_id))
    @if($request->staff_comments && ($request->user_id == Auth::user()->id || Auth::user()->hasPower('manage_submissions')))
        <h5 class="text-danger">Staff Comments ({!! $request->staff->displayName !!})</h5>
        <div class="card border-danger mb-3"><div class="card-body">{!! nl2br(htmlentities($request->staff_comments)) !!}</div></div>
    @else
        <p>No staff comment was provided.</p>
    @endif
@endif

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ set_active('designs/' . $request->id) }}" href="{{ url('designs/' . $request->id) }}">@if($request->is_complete)<i class="text-success fas fa-check-circle fa-fw mr-2"></i> @endif Status</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ set_active('designs/' . $request->id . '/comments') }}" href="{{ url('designs/' . $request->id . '/comments') }}"><i class="text-{{ $request->has_comments ? 'success far fa-circle' : 'danger fas fa-times'  }} fa-fw mr-2"></i> Comments</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ set_active('designs/' . $request->id . '/image') }}" href="{{ url('designs/' . $request->id . '/image') }}"><i class="text-{{ $request->has_image ? 'success far fa-circle' : 'danger fas fa-times'  }} fa-fw mr-2"></i> Masterlist Image</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ set_active('designs/' . $request->id . '/addons') }}" href="{{ url('designs/' . $request->id . '/addons') }}"><i class="text-{{ $request->has_addons ? 'success far fa-circle' : 'danger fas fa-times'  }} fa-fw mr-2"></i> Add-ons</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ set_active('designs/' . $request->id . '/traits') }}" href="{{ url('designs/' . $request->id . '/traits') }}"><i class="text-{{ $request->has_features ? 'success far fa-circle' : 'danger fas fa-times'  }} fa-fw mr-2"></i> Traits</a>
    </li>
</ul>