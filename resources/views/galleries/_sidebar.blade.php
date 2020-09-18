<ul>
    <li class="sidebar-header"><a href="{{ url('gallery') }}" class="card-link">Gallery</a></li>

    @if($galleryPage && $sideGallery->children->count())
        <li class="sidebar-section">
            <div class="sidebar-section-header">{{ $sideGallery->name }}: Sub-Galleries</div>
            @foreach($sideGallery->children as $child)
                <div class="sidebar-item"><a href="{{ url('gallery/'.$child->id) }}" class="{{ set_active('gallery/.$child->id') }}">{{ $child->name }}</a></div>
            @endforeach
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header">Galleries</div>
        <?php 
            use app\Models\Gallery\Gallery;
            $galleries = Gallery::whereNull('parent_id')->sort()->get(); 
        ?>
        @foreach($galleries as $gallery)
            <div class="sidebar-item"><a href="{{ url('gallery/'.$gallery->id) }}" class="{{ set_active('gallery/.$gallery->id') }}">{{ $gallery->name }}</a></div>
        @endforeach
    </li>
</ul>