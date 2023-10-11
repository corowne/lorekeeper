<ul>
    <li class="sidebar-header"><a href="{{ url('encounter-areas') }}" class="card-link">Encounter Areas</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Areas</div>
        @foreach ($areas as $area)
            <div class="sidebar-item"><a href="{{ $area->url }}"
                    class="{{ set_active('encounter-areas/' . $area->id) }}">{{ $area->name }}</a></div>
        @endforeach
    </li>
</ul>
