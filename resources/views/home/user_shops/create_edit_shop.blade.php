@extends('home.user_shops.layout')

@section('home.user_shops-title')
    My Shops
@endsection

@section('home.user_shops-content')
    {!! breadcrumbs([
        'My Shops' => 'user-shops',
        ($shop->id ? 'Edit' : 'Create') . ' Shop' => $shop->id ? 'user-shops/edit/' . $shop->id : 'user-shops/create',
    ]) !!}

    <h1>{{ $shop->id ? 'Edit' : 'Create' }} Shop
        @if ($shop->id)
            ({!! $shop->displayName !!})
            <a href="#" class="btn btn-danger float-right delete-shop-button">Delete Shop</a>
        @endif
    </h1>

    {!! Form::open(['url' => $shop->id ? 'user-shops/edit/' . $shop->id : 'user-shops/create', 'files' => true]) !!}

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
        {!! Form::checkbox('is_active', 1, $shop->id ? $shop->is_active : 1, [
            'class' => 'form-check-input',
            'data-toggle' => 'toggle',
        ]) !!}
        {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the shop will not be visible to regular users.') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($shop->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($shop->id)
        <h3>Shop Stock</h3>

        <div class="alert alert-warning text-center">Other users cannot buy items until the stock is set to visible. </div>
        @if ($shop->stock->where('quantity', '>', 0)->count())
            <p class="text-center">Quick edit your shop's stock here. Please keep in mind that any quantity set above 0 will
                REMOVE
                stock from your shop. You don't need to set a quantity to edit stock.</p>
            <hr>
            {!! Form::open(['url' => 'user-shops/quickstock/' . $shop->id]) !!}
            @include('widgets._user_shop_select')

            <div class="text-right">
                {!! Form::submit('Edit Stock', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        @else
            <div class="alert alert-warning text-center">Add stock to your shop from your inventory.</div>
        @endif

        <hr>
        <h3> Preview </h3>
        <br>
        <h1>
            {{ $shop->name }}
        </h1>
        <div class="mb-3">
            Owned by {!! $shop->user->displayName !!}
        </div>

        <div class="text-center">
            <img src="{{ $shop->shopImageUrl }}" style="max-width: 200px !important; max-height: 200px !important;"
                alt="{{ $shop->name }}" />
            <p>{!! $shop->parsed_description !!}</p>
        </div>
    @endif

@endsection

@section('scripts')
    @parent
    @include('widgets._inventory_select_js', ['readOnly' => true])
    <script>
        $('.delete-shop-button').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('user-shops/delete') }}/{{ $shop->id }}", 'Delete Shop');
        });
    </script>
@endsection
