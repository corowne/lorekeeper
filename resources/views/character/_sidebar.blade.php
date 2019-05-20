<ul>
    <li class="sidebar-header"><a href="{{ $character->url }}" class="card-link">{{ $character->fullName }}</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Character</div>
        <div class="sidebar-item"><a href="{{ $character->url . '/profile' }}" class="{{ set_active('character/'.$character->slug.'/profile') }}">Profile</a></div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Bank</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">History</div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Images</a></div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Change Log</a></div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Ownership History</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Settings</div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Edit Profile</a></div>
        <div class="sidebar-item"><a href="{{ url('#') }}" class="{{ set_active('#') }}">Transfer</a></div>
    </li>
    
</ul>