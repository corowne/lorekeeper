{{-- Image Data --}}
<div class="col-md-5 d-flex">
    
    <div class="card character-bio w-100">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" id="infoTab-{{ $image->id }}" data-toggle="tab" href="#info-{{ $image->id }}" role="tab">Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="notesTab-{{ $image->id }}" data-toggle="tab" href="#notes-{{ $image->id }}" role="tab">Notes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="creditsTab-{{ $image->id }}" data-toggle="tab" href="#credits-{{ $image->id }}" role="tab">Credits</a>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content">
            @if(!$image->is_valid)
                <div class="alert alert-danger">
                    This version of this character is outdated, and only noted here for recordkeeping purposes.
                </div>
            @endif

            {{-- Basic info  --}}
            <div class="tab-pane fade show active" id="info-{{ $image->id }}">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-4"><h5>Species</h5></div>
                    <div class="col-lg-8 col-md-6 col-8">{!! $image->species->displayName !!}</div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-4"><h5>Rarity</h5></div>
                    <div class="col-lg-8 col-md-6 col-8">{!! $image->rarity->displayName !!}</div>
                </div>
                
                <div>
                    <div><h5>Traits</h5></div>
                    <div>
                        @foreach($image->features as $feature)
                            <div><strong>{!! $feature->feature->category->displayName !!}:</strong> {!! $feature->feature->displayName !!}</div>
                        @endforeach
                    </div>
                </div>    
            </div>

            {{-- Image notes --}}
            <div class="tab-pane fade" id="notes-{{ $image->id }}">
                @if($image->parsed_description)
                    <div class="parsed-text">{!! $image->parsed_description !!}</div>
                @else 
                    <div>No additional notes given.</div>
                @endif
            </div>

            {{-- Image credits --}}
            <div class="tab-pane fade" id="credits-{{ $image->id }}">
                
                <div class="row mb-2">
                    <div class="col-lg-4 col-md-6 col-4"><h5>Design</h5></div>
                    <div class="col-lg-8 col-md-6 col-8">
                        @foreach($image->designers as $designer)
                            <div>{!! $designer->displayLink() !!}</div>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-4"><h5>Art</h5></div>
                    <div class="col-lg-8 col-md-6 col-8">
                        @foreach($image->artists as $artist)
                            <div>{!! $artist->displayLink() !!}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>