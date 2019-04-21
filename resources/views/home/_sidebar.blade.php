<ul>
    <li class="sidebar-header"><a href="{{ url('/') }}" class="card-link">Home</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Inventory</div>
        <div class="sidebar-item"><a href="{{ url('characters') }}" class="{{ set_active('characters*') }}">Characters</a></div>
        <div class="sidebar-item"><a href="{{ url('inventory') }}" class="{{ set_active('inventory*') }}">Inventory</a></div>
        <div class="sidebar-item"><a href="{{ url('bank') }}" class="{{ set_active('bank*') }}">Bank</a></div>
    </li>
</ul>