<ul>
    <li class="sidebar-header"><a href="{{ $user->url }}" class="card-link">{{ $user->name }}</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">User</div>
        <div class="sidebar-item"><a href="{{ $user->url.'/characters' }}" class="{{ set_active('user/'.$user->name.'/characters*') }}">Characters</a></div>
        <div class="sidebar-item"><a href="{{ $user->url.'/inventory' }}" class="{{ set_active('user/'.$user->name.'/inventory*') }}">Inventory</a></div>
        <div class="sidebar-item"><a href="{{ $user->url.'/bank' }}" class="{{ set_active('user/'.$user->name.'/bank*') }}">Bank</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">History</div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Character History</a></div>
        <div class="sidebar-item"><a href="{{ $user->url.'/item-logs' }}" class="{{ $user->url.'/currency-logs*' }}">Item Logs</a></div>
        <div class="sidebar-item"><a href="{{ $user->url.'/currency-logs' }}" class="{{ set_active($user->url.'/currency-logs*') }}">Currency Logs</a></div>
    </li>
    
    @if(Auth::check() && Auth::user()->hasPower('edit_user_info') && Auth::user()->canEditRank($user->rank))
        <li class="sidebar-section">
            <div class="sidebar-section-header">Admin</div>
            <div class="sidebar-item"><a href="{{ $user->adminUrl }}">Edit User</a></div>
        </li>
    @endif
</ul>