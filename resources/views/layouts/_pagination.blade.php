@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    @php
                        $instanceID = mt_rand();
                    @endphp
                    <li class="page-item pageSelectPopover{{ $instanceID }}" data-container="body" data-toggle="popover" data-placement="top" data-title="Jump to Page" data-html="true"
                        data-content='
                    <form>
                    <div class="form-group justify-content-center">
                            <input type="range" class="form-control-range custom-range" id="paginationPageRange{{ $instanceID }}" min="1" max="{{ $paginator->lastPage() }}" value="{{ $paginator->currentPage() }}" onchange="pageUpdateSelectText{{ $instanceID }}()">
                            <input type="number" id="paginationPageText{{ $instanceID }}" min="1" max="{{ $paginator->lastPage() }}" value="{{ $paginator->currentPage() }}" onchange="pageUpdateSelectRange{{ $instanceID }}()">
                            <button type="button" class="btn btn-primary" onclick="pageGo{{ $instanceID }}()">Go</button>
                        </div>
                    </form>
                    '>
                        <span class="page-link">{{ $element }}</span></li>
                    @include('layouts._pagination_js', ['ID' => $instanceID])
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
