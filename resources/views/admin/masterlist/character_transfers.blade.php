@extends('admin.layout')

@section('admin-title')
    Character Transfers
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Character Transfer Queue' => 'admin/masterlist/transfers/incoming']) !!}

    <h1>
        Character Transfers
    </h1>

    @include('admin.masterlist._header', ['tradeCount' => $tradeCount, 'transferCount' => $transferCount])

    {!! $transfers->render() !!}
    @foreach ($transfers as $transfer)
        @include('admin.masterlist._transfer', ['transfer' => $transfer])
    @endforeach
    {!! $transfers->render() !!}
@endsection


@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.transfer-action-button').on('click', function(e) {
                e.preventDefault();
                console.log("{{ url('admin/masterlist/transfer/act') }}/" + $(this).data('id') + "/" + $(this).data('action'));
                loadModal("{{ url('admin/masterlist/transfer/act') }}/" + $(this).data('id') + "/" + $(this).data('action'), 'Process Transfer');
            });
        });
    </script>
@endsection
