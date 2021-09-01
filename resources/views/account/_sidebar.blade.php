<ul>
    <li class="sidebar-header"><a href="{{ url('/') }}" class="card-link">Home</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Account</div>
        <div class="sidebar-item"><a href="{{ url('notifications') }}" class="{{ set_active('notifications') }}">Notifications</a></div>
        <div class="sidebar-item"><a href="{{ url('account/settings') }}" class="{{ set_active('account/settings') }}">Settings</a></div>
        <div class="sidebar-item"><a href="{{ url('account/aliases') }}" class="{{ set_active('account/aliases') }}">Aliases</a></div>
        <div class="sidebar-item"><a href="{{ url('account/bookmarks') }}" class="{{ set_active('account/bookmarks') }}">Bookmarks</a></div>
    </li>
</ul>