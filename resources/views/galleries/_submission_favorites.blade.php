@if ($submission)
    <ul>
        @foreach ($submission->favorites as $favorite)
            <li>{!! $favorite->user->displayName !!}</li>
        @endforeach
    </ul>
@else
    Invalid submission selected.
@endif
