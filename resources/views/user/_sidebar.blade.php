<ul>
    <li class="sidebar-header"><a href="{{ url($user->url) }}" class="card-link">{{ $user->name }}</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Inventory</div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Characters</a></div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Items</a></div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Bank</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">History</div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Character History</a></div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Item Transfers</a></div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Currency Transfers</a></div>
    </li>
    
    @if(Auth::user()->hasPower('edit_user_info') && Auth::user()->canEditRank($user->rank))
        <li class="sidebar-section">
            <div class="sidebar-section-header">Admin</div>
            <div class="sidebar-item"><a href="{{ $user->adminUrl }}">Edit User</a></div>
        </li>
    @endif
</ul>