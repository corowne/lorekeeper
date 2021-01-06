@extends('account.layout')

@section('account-title') Bookmarks @endsection

@section('account-content')
{!! breadcrumbs(['My Account' => Auth::user()->url, 'Character Bookmarks' => 'bookmarks']) !!}

<h1>Character Bookmarks</h1>

<p>Bookmarks allow you to keep track of characters that other users own without notifying them in any way. You can add new bookmarks by visiting the character's page and clicking the Bookmark button. You cannot bookmark your own characters, but characters you have bookmarked that are transferred to you will preserve the bookmarks until you delete them. Bookmarks on characters you own will not give you notifications.</p>

<div class="text-right mb-3">
    <div class="btn-group">
        <button type="button" class="btn btn-secondary active thumb-view-button" data-toggle="tooltip" title="Thumbnail View" alt="Grid View"><i class="fas fa-th-list"></i></button>
        <button type="button" class="btn btn-secondary list-view-button" data-toggle="tooltip" title="Compact View" alt="List View"><i class="fas fa-bars"></i></button>
    </div>
</div>

{!! $bookmarks->render() !!}
<div class="table-responsive mb-3">
    <table class="table table-sm bookmark-table mb-0">
        <thead>
            <tr>
                <th class="thumbnail-hide bookmark-thumbnail"></th>
                <th class="bookmark-info">Info</th>
                <th class="bookmark-comment">Comment</th>
                <th class="bookmark-notifications">Notify When...</th>
                <th class="bookmark-actions"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookmarks as $bookmark)
                <tr>
                    <td class="thumbnail-hide">
                        <div>
                            <a href="{{ $bookmark->character->url }}"><img src="{{ $bookmark->character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                        </div>
                    </td>
                    <td>
                        <h5 class="mb-0">{!! $bookmark->character->displayName !!}</h5>
                        {!! $bookmark->character->image->species_id ? $bookmark->character->image->species->displayName : 'No Species' !!} ・ {!! $bookmark->character->image->rarity_id ? $bookmark->character->image->rarity->displayName : 'No Rarity' !!} ・ {!! $bookmark->character->displayOwner !!}
                        
                        @if($bookmark->character->is_gift_art_allowed && !$bookmark->character->is_myo_slot)
                            <div><i class="text-success far fa-circle fa-fw mr-2"></i> Gift art is allowed</div>
                        @endif
                        @if($bookmark->character->is_gift_writing_allowed && !$bookmark->character->is_myo_slot)
                            <div><i class="text-success far fa-circle fa-fw mr-2"></i> Gift writing is allowed</div>
                        @endif
                        @if($bookmark->character->is_trading)
                            <div><i class="text-success far fa-circle fa-fw mr-2"></i> Open for trades</div>
                        @endif
                    </td>
                    <td>
                        {!! nl2br(htmlentities($bookmark->comment)) !!}
                    </td>
                    <td>
                        <i class="fas fa-exchange-alt fa-lg fa-fw mr-2 {{ $bookmark->notify_on_trade_status ? 'text-success' : 'text-danger' }}" data-toggle="tooltip" title="Open For Trade status changes"></i>
                        <i class="fas fa-gift fa-lg fa-fw mr-2 {{ $bookmark->notify_on_gift_art_status ? 'text-success' : 'text-danger' }}" data-toggle="tooltip" title="Gift Art Allowed status changes"></i>
                        <i class="fas fa-pen-square fa-lg fa-fw mr-2 {{ $bookmark->notify_on_gift_writing_status ? 'text-success' : 'text-danger' }}" data-toggle="tooltip" title="Gift Writing Allowed status changes"></i>
                        <i class="fas fa-user fa-lg fa-fw mr-2 {{ $bookmark->notify_on_transfer ? 'text-success' : 'text-danger' }}" data-toggle="tooltip" title="Character's owner changes"></i>
                        <i class="far fa-image fa-lg fa-fw mr-2 {{ $bookmark->notify_on_image ? 'text-success' : 'text-danger' }}" data-toggle="tooltip" title="A new image is uploaded"></i>

                    </td>
                    <td class="text-right">
                        <a href="#" class="btn btn-outline-primary btn-sm edit-bookmark-button" data-id="{{ $bookmark->id }}">Edit</a>
                        <a href="#" class="btn btn-outline-danger btn-sm delete-bookmark-button" data-id="{{ $bookmark->id }}">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if(!count($bookmarks))
    <div class="text-center">No bookmarks. You can bookmark characters from their respective pages.</div>
@endif

{!! $bookmarks->render() !!}

@endsection
@section('scripts')
@parent
<script>
    $( document ).ready(function(){
        var $thumbButton = $('.thumb-view-button');
        var $thumbnails = $('.thumbnail-hide');
        var $listButton = $('.list-view-button');

        var view = null;

        initView();

        $thumbButton.on('click', function(e) {
            e.preventDefault();
            setView('thumbs');
        });
        $listButton.on('click', function(e) {
            e.preventDefault();
            setView('list');
        });

        function initView()
        {
            view = window.localStorage.getItem('lorekeeper_bookmark_view');
            if(!view) view = 'thumbs';
            setView(view);
        }

        function setView(status)
        {
            view = status;

            if(view == 'thumbs') {
                $thumbnails.removeClass('hide');
                $thumbButton.addClass('active');
                $listButton.removeClass('active');
                window.localStorage.setItem('lorekeeper_bookmark_view', 'thumbs');
            }   
            else if (view == 'list') {
                $listButton.addClass('active');
                $thumbnails.addClass('hide');
                $thumbButton.removeClass('active');
                window.localStorage.setItem('lorekeeper_bookmark_view', 'list');
            }
        }

        $('.edit-bookmark-button').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            loadModal("{{ url('account/bookmarks/edit') }}" + '/' + $this.data('id'), 'Edit Bookmark');
        });
        
        $('.delete-bookmark-button').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            loadModal("{{ url('account/bookmarks/delete') }}" + '/' + $this.data('id'), 'Delete Bookmark');
        });
    });


</script>
@endsection