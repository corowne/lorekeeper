<ul>
    <li class="sidebar-header"><a href="{{ url('/') }}" class="card-link">Home</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Inventory</div>
        <div class="sidebar-item"><a href="{{ url('characters') }}" class="{{ set_active('characters') }}">My Characters</a></div>
        <div class="sidebar-item"><a href="{{ url('characters/myos') }}" class="{{ set_active('characters/myos') }}">My MYO Slots</a></div>
        <div class="sidebar-item"><a href="{{ url('inventory') }}" class="{{ set_active('inventory*') }}">Inventory</a></div>
        <div class="sidebar-item"><a href="{{ url('bank') }}" class="{{ set_active('bank*') }}">Bank</a></div>
        <div class="sidebar-item"><a href="{{ url('level') }}" class="{{ set_active('level*') }}">Level Area</a></div>
        @if(Auth::check())<div class="sidebar-item"><a href="{{ url(Auth::user()->url . '/level') }}" class="{{ set_active('level') }}">Level Logs</a></div>@endif
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Activity</div>
        <div class="sidebar-item"><a href="{{ url('submissions') }}" class="{{ set_active('submissions*') }}">Prompt Submissions</a></div>
        <div class="sidebar-item"><a href="{{ url('claims') }}" class="{{ set_active('claims*') }}">Claims</a></div>
        <div class="sidebar-item"><a href="{{ url('characters/transfers/incoming') }}" class="{{ set_active('characters/transfers*') }}">Character Transfers</a></div>
        <div class="sidebar-item"><a href="{{ url('trades/open') }}" class="{{ set_active('trades/open*') }}">Trades</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Reports</div>
        <div class="sidebar-item"><a href="{{ url('reports') }}" class="{{ set_active('reports*') }}">Reports</a></div>
    </li>
</ul>