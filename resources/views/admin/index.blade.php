@extends('admin.layout')

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Home' => 'admin']) !!}

<h1>Admin Dashboard</h1>
<p>Testing testing...add some commonly-used links here, e.g. create character, go to approval queue, etc.</p>
<div class="row">
    <div class="col-sm-6">        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Prompt Submissions @if($submissionCount)<span class="badge badge-primary">{{ $submissionCount }}</span>@endif</h5>
                <p class="card-text">
                    @if($submissionCount)
                        {{ $submissionCount }} submission{{ $submissionCount == 1 ? '' : 's' }} awaiting processing.
                    @else 
                        The submission queue is clear. Hooray!
                    @endif
                </p>
                <div class="text-right">
                    <a href="{{ url('admin/submissions/pending') }}" class="card-link">View Queue <span class="fas fa-caret-right ml-1"></span></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Claims @if($claimCount)<span class="badge badge-primary">{{ $claimCount }}</span>@endif</h5>
                <p class="card-text">
                    @if($claimCount)
                        {{ $claimCount }} claim{{ $claimCount == 1 ? '' : 's' }} awaiting processing.
                    @else 
                        The claim queue is clear. Hooray!
                    @endif
                </p>
                <div class="text-right">
                    <a href="{{ url('admin/claims/pending') }}" class="card-link">View Queue <span class="fas fa-caret-right ml-1"></span></a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
