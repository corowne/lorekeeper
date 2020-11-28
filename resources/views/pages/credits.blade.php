@extends('layouts.app')

@section('title') Credits @endsection

@section('content')
{!! breadcrumbs(['Credits' => url('credits') ]) !!}
<h1>Credits</h1>

<div class="site-page-content parsed-text">
    {!! $credits->parsed_text !!}
</div>

<hr>

<h5 class="mb-0">Extensions</h5>
<p class="mb-2">These extensions were coded by the Lorekeeper community.</p>

<div class="extensions">
    @foreach($extensions as $extension)
        <p class="mb-0">
            <a href="http://wiki.lorekeeper.me/index.php?title=Extensions:{{ $extension->wiki_key }}">
                <strong>{{ str_replace('_',' ',$extension->wiki_key) }}</strong>
                <small>v. {{ $extension->version }}</small>
            </a>
            by 
            <?php $extension->array = json_decode($extension->creators,true); $extension->end = end($extension->array); ?>
            @foreach(json_decode($extension->creators,true) as $name => $url)
                <a href="{{ $url }}">{{ $name }}</a>{{$extension->end != $extension->array[$name] ? ',' : '' }}
            @endforeach 
        </p>
    @endforeach
</div>

@endsection
