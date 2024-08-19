@extends('admin.layout')

@section('admin-title')
    {{ $shop->id ? 'Edit' : 'Create' }} Shop
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Shops' => 'admin/data/shops', ($shop->id ? 'Edit' : 'Create') . ' Shop' => $shop->id ? 'admin/data/shops/edit/' . $shop->id : 'admin/data/shops/create']) !!}

    <h1>{{ $shop->id ? 'Edit' : 'Create' }} Shop
        @if ($shop->id)
            ({!! $shop->displayName !!})
            <a href="#" class="btn btn-danger float-right delete-shop-button">Delete Shop</a>
        @endif
    </h1>

    {!! Form::open(['url' => $shop->id ? 'admin/data/shops/edit/' . $shop->id : 'admin/data/shops/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $shop->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Shop Image (Optional)') !!} {!! add_help('This image is used on the shop index and on the shop page as a header.') !!}
        <div>{!! Form::file('image') !!}</div>
        <div class="text-muted">Recommended size: None (Choose a standard size for all shop images)</div>
        @if ($shop->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $shop->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_active', 1, $shop->id ? $shop->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the shop will not be visible to regular users.') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($shop->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($shop->id)
        <h3>Shop Stock</h3>
        {!! Form::open(['url' => 'admin/data/shops/stock/' . $shop->id]) !!}
        <div class="text-right mb-3">
            <a href="#" class="add-stock-button btn btn-outline-primary">Add Stock</a>
        </div>
        <div id="shopStock">
            @foreach ($shop->stock as $key => $stock)
                @include('admin.shops._stock', ['stock' => $stock, 'key' => $key])
            @endforeach
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
        <div class="" id="shopStockData">
            @include('admin.shops._stock', ['stock' => null, 'key' => 0])
        </div>
    @endif

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $shopStock = $('#shopStock');
            var $stock = $('#shopStockData').find('.stock');

            $('.delete-shop-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/shops/delete') }}/{{ $shop->id }}", 'Delete Shop');
            });
            $('.add-stock-button').on('click', function(e) {
                e.preventDefault();

                var clone = $stock.clone();
                $shopStock.append(clone);
                clone.removeClass('hide');
                attachStockListeners(clone);
                refreshStockFieldNames();
            });

            attachStockListeners($('#shopStock .stock'));

            function attachStockListeners(stock) {
                stock.find('.stock-toggle').bootstrapToggle();
                stock.find('.stock-limited').on('change', function(e) {
                    var $this = $(this);
                    if ($this.is(':checked')) {
                        $this.parent().parent().parent().parent().find('.stock-limited-quantity').removeClass('hide');
                    } else {
                        $this.parent().parent().parent().parent().find('.stock-limited-quantity').addClass('hide');
                    }
                });
                stock.find('.remove-stock-button').on('click', function(e) {
                    e.preventDefault();
                    $(this).parent().parent().parent().remove();
                    refreshStockFieldNames();
                });
                stock.find('.card-body [data-toggle=tooltip]').tooltip({
                    html: true
                });
            }

            function refreshStockFieldNames() {
                $('.stock').each(function(index) {
                    var $this = $(this);
                    var key = index;
                    $this.find('.stock-field').each(function() {
                        $(this).attr('name', $(this).data('name') + '[' + key + ']');
                    });
                });
            }
        });
    </script>
@endsection
