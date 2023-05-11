@if (Config::get('lorekeeper.extensions.show_all_recent_submissions.enable') && Config::get('lorekeeper.extensions.show_all_recent_submissions.section_on_front'))
    <div class="card my-2 text-center">
        <div class="card-header">
            <h5>Recent Gallery Submissions</h5>
        </div>
        <div class="card-body">
            @if (count($gallerySubmissions))
                <div class="row">
                    @foreach ($gallerySubmissions as $gallerySubmission)
                        <div class="col-md-3 col-6 profile-inventory-item">
                            @include('galleries._thumb', ['submission' => $gallerySubmission, 'gallery' => false])
                        </div>
                    @endforeach
                    <div class="col-12"><a class="float-right" href="gallery/all">View all Recent Submissions...</a></div>
                </div>
            @else
                <div>No Gallery Submissions.</div>
            @endif
        </div>
    </div>
@endif
