<ul>
    <li class="sidebar-header"><a href="{{ url('sales') }}" class="card-link">Sales</a></li>
    <li class="sidebar-section">
        <div class="sidebar-section-header">For Sale</div>
        @foreach (App\Models\Sales\Sales::visible()->orderBy('updated_at', 'DESC')->where('is_open', 1)->get() as $sales)
            @php $salelink = 'sales/'.$sales->slug; @endphp
            <div class="sidebar-item"><a href="{{ $sales->url }}" class="{{ set_active($salelink) }}">{{ $sales->title }}</a></div>
        @endforeach
        @if (isset($saleses))
    <li class="sidebar-section">
        <div class="sidebar-section-header">On This Page</div>
        @foreach ($saleses as $sales)
            @php $salelink = 'sales/'.$sales->slug; @endphp
            <div class="sidebar-item"><a href="{{ $sales->url }}" class="{{ set_active($salelink) }}">{{ '[' . ($sales->is_open ? 'OPEN' : 'CLOSED') . '] ' . $sales->title }}</a></div>
        @endforeach
    </li>
@else
    <li class="sidebar-section">
        <div class="sidebar-section-header">Recent Sales</div>
        @foreach (App\Models\Sales\Sales::visible()->orderBy('updated_at', 'DESC')->take(10)->get() as $sales)
            @php $salelink = 'sales/'.$sales->slug; @endphp
            <div class="sidebar-item"><a href="{{ $sales->url }}" class="{{ set_active($salelink) }}">{{ '[' . ($sales->is_open ? 'OPEN' : 'CLOSED') . '] ' . $sales->title }}</a></div>
        @endforeach
    </li>
    @endif
</ul>
