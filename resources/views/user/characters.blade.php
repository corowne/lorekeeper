@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Characters @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Characters' => $user->url . '/characters']) !!}

<h1>
    {!! $user->displayName !!}'s Characters
</h1>


@if($characters->count())
        @foreach($characters as $categoryId=>$categoryCharacters)
            <div class="card mb-3 inventory-category">
                <h5 class="card-header inventory-header">
                    {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
                </h5>
                <div class="card-body pb-0 inventory-body">
                    @foreach($categoryCharacters->chunk(4) as $chunk)
                        <div class="row mb-3">
                            @foreach($chunk as $character) 
                                <div class="col-md-3 col-6 text-center mb-2">
                                    <div>
                                        <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                                    </div>
                                    <div class="mt-1 h5">
                                        {!! $character->displayName !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
@else
    <p>No characters found.</p> 
@endif

@endsection
