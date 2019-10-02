<ul>
    <li class="sidebar-header"><a href="{{ url('shops') }}" class="card-link">Shops</a></li>
    
    @if(Auth::check())
        <li class="sidebar-section">
            <div class="sidebar-section-header">History</div>
            <div class="sidebar-item"><a href="{{ url('shops/history') }}" class="{{ set_active('shops/history') }}">My Purchase History</a></div>
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header">Shops</div>
        @foreach($shops as $shop)
            <div class="sidebar-item"><a href="{{ $shop->url }}" class="{{ set_active('shops/'.$shop->id) }}">{{ $shop->name }}</a></div>
        @endforeach
    </li>
</ul>