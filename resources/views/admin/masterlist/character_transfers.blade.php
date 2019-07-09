@extends('admin.layout')

@section('admin-title') Character Transfers @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Character Transfer Queue' => 'admin/masterlist/create-character']) !!}

<h1>
    Character Transfers
</h1>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/masterlist/transfers/incoming*') }}" href="{{ url('admin/masterlist/transfers/incoming') }}">Incoming</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/masterlist/transfers/completed*') }}" href="{{ url('admin/masterlist/transfers/completed') }}">Completed</a>
  </li>
</ul>

{!! $transfers->render() !!}
@foreach($transfers as $transfer)
    @include('admin.masterlist._transfer', ['transfer' => $transfer])
@endforeach
{!! $transfers->render() !!}


@endsection


@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.transfer-action-button').on('click', function(e) {
        e.preventDefault();
        console.log("{{ url('admin/masterlist/transfer/act') }}/" + $(this).data('id') + "/" + $(this).data('action'));
        loadModal("{{ url('admin/masterlist/transfer/act') }}/" + $(this).data('id') + "/" + $(this).data('action') , 'Process Transfer');
    });
});
    
</script>
@endsection