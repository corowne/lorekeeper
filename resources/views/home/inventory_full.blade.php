@extends('home.layout')

@section('home-title')
    Full Inventory
@endsection

@section('home-content')
    {!! breadcrumbs(['Inventory' => 'inventory', 'Full Inventory' => 'inventory-full']) !!}

    <h1>
        Full Inventory
    </h1>

    <p>This is your FULL inventory. Click on an item name to view more details on the item, click on the word 'stack' to see the actions you can perform on it.</p>

    @foreach ($items as $categoryId => $categoryItems)
        <div class="card mb-2">
            <h5 class="card-header">
                {!! isset($categories[$categoryId]) ? '<a href="' . $categories[$categoryId]->searchUrl . '">' . $categories[$categoryId]->name . '</a>' : 'Miscellaneous' !!}
                <a class="small inventory-collapse-toggle collapse-toggle" href="#categoryId_{!! isset($categories[$categoryId]) ? $categories[$categoryId]->id : 'miscellaneous' !!}" data-toggle="collapse">Show</a>
            </h5>
            <div class="card-body p-2 collapse show row" id="categoryId_{!! isset($categories[$categoryId]) ? $categories[$categoryId]->id : 'miscellaneous' !!}">
                @foreach ($categoryItems as $itemId => $itemtype)
                    <div class="col-lg-3 col-sm-4 col-12">
                        @if ($categoryItems[$itemId]->first()->has_image)
                            <img src="{{ $categoryItems[$itemId]->first()->imageUrl }}" style="height: 25px;" alt="{{ $categoryItems[$itemId]->first()->name }}" />
                        @endif
                        <a href="{{ $categoryItems[$itemId]->first()->idUrl }}">{{ $categoryItems[$itemId]->first()->name }}</a>
                        <ul class="mb-0">
                            @foreach ($itemtype as $item)
                                <li>
                                    @if (isset($item->pivot->user_id))
                                        <a class="invuser" data-id="{{ $item->pivot->id }}" data-name="{{ $user->name }}'s {{ $item->name }}" href="#">Stack</a> of x{{ $item->pivot->count }} in <a href="/inventory">your inventory</a>.
                                    @else
                                        @foreach ($characters as $char)
                                            @if ($char->id == $item->pivot->character_id)
                                                @php
                                                    $charaname = $char->name ? $char->name : $char->slug;
                                                    $charalink = $char->url;
                                                @endphp
                                            @endif
                                        @endforeach
                                        <a class="invchar" data-id="{{ $item->pivot->id }}" data-name="{{ $charaname }}'s {{ $item->name }}" href="#">Stack</a> of x{{ $item->pivot->count }} in <a
                                            href="{{ $charalink }}">{{ $charaname }}</a>'s inventory.
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.invuser').on('click', function(e) {
                e.preventDefault();
                var $parent = $(this);
                loadModal("{{ url('items') }}/" + $parent.data('id'), $parent.data('name'));
            });

            $('.invchar').on('click', function(e) {
                e.preventDefault();
                var $parent = $(this);
                loadModal("{{ url('items') }}/character/" + $parent.data('id'), $parent.data('name'));
            });
        });
    </script>
@endsection
