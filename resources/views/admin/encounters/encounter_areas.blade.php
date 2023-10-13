@extends('admin.layout')

@section('admin-title')
    Encounter Areas
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Encounter Areas' => 'admin/data/encounters/areas']) !!}

    <h1>Encounter Areas</h1>

    <p>Encounter areas are specific areas that users can explore. You can assign each area its own individual encounters
        that users can discover, each with rarity/weighting.</p>

    @if (!count($encounters))
        <div class="alert alert-danger">You can't create an area without some <a
                href="{{ url('admin/data/encounters') }}">encounters</a> to fill it!</div>
    @else
        <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/encounters/areas/create') }}"><i
                    class="fas fa-plus"></i> Create New Encounter Area</a></div>
        @if (!count($areas))
            <p>No encounter areas found.</p>
        @else
            <div class="row ml-md-2">
                <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
                    <div class="col-4 col-md-2 font-weight-bold">Name</div>
                    <div class="col-4 col-md-3 font-weight-bold">Active?</div>
                     <div class="col-4 col-md-3 font-weight-bold">Start</div>
                      <div class="col-4 col-md-3 font-weight-bold">End</div>
                </div>
                @foreach ($areas as $area)
                    <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
                        <div class="col-5 col-md-2 text-truncate">
                            {!! $area->has_thumbnail
                                ? '<img src="' . $area->thumbImageUrl . '" class="img-fluid mr-2" style="height: 2em;" />'
                                : '' !!}{!! $area->has_image ? '<img src="' . $area->imageUrl . '" class="img-fluid mr-2" style="height: 2em;" />' : '' !!}{!! $area->name !!}
                        </div>
                        <div class="col-5 col-md-3 text-truncate">
                           {!! $area->is_active ? '<i class="text-success fas fa-check"></i>' : '' !!}
                        </div>
                        <div class="col-5 col-md-3 text-truncate">
                          {!! $area->start_at ? pretty_date($area->start_at) : '-' !!}
                        </div>
                        <div class="col-5 col-md-3 text-truncate">
                           {!! $area->end_at ? pretty_date($area->end_at) : '-' !!}
                        </div>
                        <div class="col-3 col-md-1 text-right">
                            <a href="{{ url('admin/data/encounters/areas/edit/' . $area->id) }}"
                                class="btn btn-primary">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

@endsection

@section('scripts')
    @parent
@endsection
