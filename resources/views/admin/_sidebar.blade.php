<ul>
    <li class="sidebar-header"><a href="{{ url('admin') }}" class="card-link">Admin Home</a></li>
    
    @foreach(Config::get('lorekeeper.admin_sidebar') as $key => $section)
        <li class="sidebar-section">
            <div class="sidebar-section-header">{{ str_replace(' ', '', $key) }}</div>
            
            @foreach($section as $item)
                <div class="sidebar-item"><a href="{{ url($item['url']) }}" class="{{ set_active($item['url']) }}">{{ $item['name'] }}</a></div>
            @endforeach
        </li>
    @endforeach

</ul>