<ul>
    <li class="sidebar-header"><a href="{{ url('masterlist') }}" class="card-link">Masterlist</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Masterlist</div>
        <div class="sidebar-item"><a href="{{ url('masterlist') }}" class="{{ set_active('masterlist*') }}">Characters</a></div>
        <div class="sidebar-item"><a href="{{ url('myos') }}" class="{{ set_active('myos*') }}">MYO Slots</a></div>
    </li>
    @if(isset($sublists) && $sublists->count() > 0)
        <li class="sidebar-section">
            <div class="sidebar-section-header">Sub Masterlists</div>
            @foreach($sublists as $sublist)
            <div class="sidebar-item"><a href="{{ url('sublist/'.$sublist->key) }}" class="{{ set_active('sublist/'.$sublist->key) }}">{{ $sublist->name }}</a></div>
            @endforeach
        </li>
    @endif
</ul>