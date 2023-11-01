<ul>
@if(Auth::check())
        <li class="sidebar-section">
            <div class="sidebar-section-header">History</div>
            <div class="sidebar-item"><a href="{{ url('user-shops/history') }}" class="{{ set_active('user-shops/history*') }}">Purchase History</a></div>
            <div class="sidebar-section-header">My Currencies</div>
            @foreach(Auth::user()->getCurrencies(true) as $currency)
                <div class="sidebar-item pr-3">{!! $currency->display($currency->quantity) !!}</div>
            @endforeach
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header">User Shops</div>
        <div class="sidebar-item"><a href="{{ url('user-shops') }}" class="{{ set_active('user-shops') }}">My Shops</a></div>
        <div class="sidebar-item"><a href="{{ url('user-shops/shop-index') }}" class="{{ set_active('user-shops/shop-index*') }}">All User Shops</a></div>
        <div class="sidebar-item"><a href="{{ url('user-shops/item-search') }}" class="{{ set_active('user-shops/item-search*') }}">Search For Item</a></div>
</li>
</ul>