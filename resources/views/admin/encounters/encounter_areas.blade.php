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
            <table class="table table-sm area-table">
                <tbody>
                    @foreach ($areas as $area)
                        <tr data-id="{{ $area->id }}">
                            <td>
                                <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                                {!! $area->displayName !!}
                            </td>
                            <td class="text-right">
                                <a href="{{ url('admin/data/encounters/areas/edit/' . $area->id) }}"
                                    class="btn btn-primary">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

@endsection

@section('scripts')
    @parent
@endsection
