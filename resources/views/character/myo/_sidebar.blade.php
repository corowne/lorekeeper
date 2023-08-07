<ul>
    <li class="sidebar-header"><a href="{{ $character->url }}" class="card-link">{{ $character->fullName }}</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">Character</div>
        <div class="sidebar-item"><a href="{{ $character->url . '/profile' }}" class="{{ set_active('myo/' . $character->id . '/profile') }}">Profile</a></div>
    </li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">History</div>
        <div class="sidebar-item"><a href="{{ $character->url . '/change-log' }}" class="{{ set_active('myo/' . $character->id . '/change-log') }}">Change Log</a></div>
        <div class="sidebar-item"><a href="{{ $character->url . '/ownership' }}" class="{{ set_active('myo/' . $character->id . '/ownership') }}">Ownership History</a></div>
    </li>
    @if (Auth::check() && (Auth::user()->id == $character->user_id || Auth::user()->hasPower('manage_characters')))
        <li class="sidebar-section">
            <div class="sidebar-section-header">Settings</div>
            <div class="sidebar-item"><a href="{{ $character->url . '/profile/edit' }}" class="{{ set_active('myo/' . $character->id . '/profile/edit') }}">Edit Profile</a></div>
            <div class="sidebar-item"><a href="{{ $character->url . '/transfer' }}" class="{{ set_active('myo/' . $character->id . '/transfer') }}">Transfer</a></div>
            @if (Auth::user()->id == $character->user_id)
                <div class="sidebar-item"><a href="{{ $character->url . '/approval' }}" class="{{ set_active('myo/' . $character->id . '/approval') }}">Submit MYO Design</a></div>
            @endif
        </li>
    @endif
</ul>
