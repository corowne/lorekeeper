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
        @auth
            @if (Auth::user()->shops()->count() && Settings::get('user_shop_limit') == 1)
                <div class="sidebar-item">
                    <a href="{{ url(Auth::user()->shops()->first()->editUrl) }}" class="{{ set_active(Auth::user()->shops()->first()->editUrl) }}">My Shop</a>
                </div>
            @else
                <div class="sidebar-item"><a href="{{ url('user-shops') }}" class="{{ set_active('user-shops') }}">My Shops</a></div>
            @endif
        @endauth
        <div class="sidebar-item"><a href="{{ url('user-shops/shop-index') }}" class="{{ set_active('user-shops/shop-index*') }}">All User Shops</a></div>
        <div class="sidebar-item"><a href="{{ url('user-shops/item-search') }}" class="{{ set_active('user-shops/item-search*') }}">Search For Item</a></div>
</li>
</ul>