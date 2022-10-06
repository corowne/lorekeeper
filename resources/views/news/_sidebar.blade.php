<ul>
    <li class="sidebar-header"><a href="{{ url('news') }}" class="card-link">News</a></li>
    @if (isset($newses))
        <li class="sidebar-section">
            <div class="sidebar-section-header">On This Page</div>
            @foreach ($newses as $news)
                @php $newslink = 'news/'.$news->slug; @endphp
                <div class="sidebar-item"><a href="{{ $news->url }}" class="{{ set_active($newslink) }}">{{ $news->title }}</a></div>
            @endforeach
        </li>
    @else
        <li class="sidebar-section">
            <div class="sidebar-section-header">Recent News</div>
            @foreach (App\Models\News::visible()->orderBy('updated_at', 'DESC')->take(10)->get() as $news)
                @php $newslink = 'news/'.$news->slug; @endphp
                <div class="sidebar-item"><a href="{{ $news->url }}" class="{{ set_active($newslink) }}">{{ $news->title }}</a></div>
            @endforeach
        </li>
    @endif
</ul>
