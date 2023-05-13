<ul>
    <li class="sidebar-header"><a href="{{ url('/') }}" class="card-link">Home</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Inventory</div>
        <div class="sidebar-item"><a href="{{ url('characters') }}" class="{{ set_active('characters') }}">My Characters</a></div>
        <div class="sidebar-item"><a href="{{ url('characters/myos') }}" class="{{ set_active('characters/myos') }}">My MYO Slots</a></div>
        <div class="sidebar-item"><a href="{{ url('inventory') }}" class="{{ set_active('inventory*') }}">Inventory</a></div>
        <div class="sidebar-item"><a href="{{ url('bank') }}" class="{{ set_active('bank*') }}">Bank</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Activity</div>
        <div class="sidebar-item"><a href="{{ url('submissions') }}" class="{{ set_active('submissions*') }}">Prompt Submissions</a></div>
        <div class="sidebar-item"><a href="{{ url('claims') }}" class="{{ set_active('claims*') }}">Claims</a></div>
        <div class="sidebar-item"><a href="{{ url('characters/transfers/incoming') }}" class="{{ set_active('characters/transfers*') }}">Character Transfers</a></div>
        <div class="sidebar-item"><a href="{{ url('trades/open') }}" class="{{ set_active('trades/open*') }}">Trades</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">User Shops</div>
        <div class="sidebar-item"><a href="{{ url('usershops') }}" class="{{ set_active('usershops*') }}">My Shops</a></div>
        <div class="sidebar-item"><a href="{{ url('usershops/shop-index') }}" class="{{ set_active('usershops/shop-index*') }}">All User Shops</a></div>
        <div class="sidebar-item"><a href="{{ url('usershops/item-search') }}" class="{{ set_active('usershops/item-search*') }}">Search For Item</a></div>
        <div class="sidebar-item"><a href="{{ url('usershops/history') }}" class="{{ set_active('usershops/history*') }}">Purchase History</a></div>
</li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Reports</div>
        <div class="sidebar-item"><a href="{{ url('reports') }}" class="{{ set_active('reports*') }}">Reports</a></div>
    </li>
</ul>