@php
    if (isset($approved) && $approved) {
        if (isset($type)) {
            $comments = $model->approvedComments->where('type', $type);
        } else {
            $comments = $model->approvedComments->where('type', 'User-User');
        }
    } else {
        if (isset($type)) {
            $comments = $model->commentz->where('type', $type);
        } else {
            $comments = $model->commentz->where('type', 'User-User');
        }
    }
@endphp

@if (!isset($type) || $type == 'User-User')
    <div class="row">
        <div class="{{ !isset($type) || $type == 'User-User' ? 'h2' : 'hide' }}">
            Comments
        </div>

        <div class="ml-auto">
            <div class="form-inline justify-content-end">
                <div class="form-group ml-3 mb-3">
                    {!! Form::select(
                        'sort',
                        [
                            'newest' => 'Newest First',
                            'oldest' => 'Oldest First',
                        ],
                        Request::get('sort') ?: 'newest',
                        ['class' => 'form-control', 'id' => 'sort'],
                    ) !!}
                </div>
                <div class="form-group ml-3 mb-3">
                    {!! Form::select(
                        'perPage',
                        [
                            5 => '5 Per Page',
                            10 => '10 Per Page',
                            25 => '25 Per Page',
                            50 => '50 Per Page',
                            100 => '100 Per Page',
                        ],
                        Request::get('perPage') ?: 5,
                        ['class' => 'form-control', 'id' => 'perPage'],
                    ) !!}
                </div>
            </div>
        </div>
    </div>
@endif
<div id="comments">
    <div class="justify-content-center text-center mb-2">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
    </div>
</div>

@auth
    @include('comments._form')
@else
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Authentication required</h5>
            <p class="card-text">You must log in to post a comment.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
        </div>
    </div>
@endauth

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: '.comment-wysiwyg',
                height: 250,
                menubar: false,
                convert_urls: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen spoiler',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | code',
                content_css: [
                    '{{ asset('css/app.css') }}',
                    '{{ asset('css/lorekeeper.css') }}'
                ],
                spoiler_caption: 'Toggle Spoiler',
                target_list: false
            });

            var firstTime = 1;

            function sortComments() {
                $('#comments').fadeOut();
                $.ajax({
                    url: "{{ url('sort-comments/' . base64_encode(urlencode(get_class($model))) . '/' . $model->getKey()) }}",
                    type: 'GET',
                    data: {
                        url: '{{ url()->current() }}',
                        allow_dislikes: '{{ isset($allow_dislikes) ? $allow_dislikes : false }}',
                        approved: '{{ isset($approved) ? $approved : false }}',
                        type: '{{ isset($type) ? $type : null }}',
                        sort: $('#sort').val(),
                        perPage: $('#perPage').val(),
                        page: '{{ request()->query('page') }}',
                    },
                    success: function(data) {
                        $('#comments').html(data);
                        // update current url to reflect sort change
                        var url = new URL(window.location.href);
                        if (firstTime != 1) {
                            url.searchParams.set('sort', $('#sort').val());
                            url.searchParams.set('perPage', $('#perPage').val());
                            
                            window.history.pushState({}, '', url);
                        } else {
                            firstTime = 0;
                        }

                        $('#comments').fadeIn();
                    }
                });
            }

            $('#sort').change(function() {
                sortComments();
            });

            $('#perPage').change(function() {
                sortComments();
            });

            sortComments(); // initial sort
        });
    </script>
@endsection
