@if(isset($gallery->parent_id) && $gallery->parent)
  <div class="col-12 column mw-100 pr-0 pt-4" style="flex-basis: 100%;">
@else
  <div class="pt-4" style="flex-basis: 100%;">
@endif

<div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
  <div class="col-6 col-md-1">{!! $gallery->submissions_open ? '<i class="text-success fas fa-check"></i>' : '-' !!}</div>
  <div class="col-6 col-md-2">
        <h6>
            @if(isset($gallery->parent_id) && $gallery->parent) <i class="fas fa-caret-right"></i> @endif
            {!! $gallery->displayName !!}
        </h6>
  </div>
  <div class="col-6 col-md-1">{!! Settings::get('gallery_submissions_reward_currency') ? ($gallery->currency_enabled ? '<i class="text-success fas fa-check"></i>' : '-') : '' !!}</div>
  <div class="col-6 col-md-2">{!! Settings::get('gallery_submissions_require_approval') ? ($gallery->votes_required ? $gallery->votes_required : '-') : '' !!}</div>
  <div class="col-4 col-md-2">
    {!! $gallery->start_at ? pretty_date($gallery->start_at) : '-' !!}
  </div>
  <div class="col-4 col-md-2">
    {!! $gallery->end_at ? pretty_date($gallery->end_at) : '-' !!}
  </div>
  <div class="col-6 col-md-2 text-right"><a href="{{ url('admin/data/galleries/edit/'.$gallery->id) }}" class="btn btn-primary">Edit</a></div>
</div>

@if($gallery->children->count() > 0)
    @foreach($gallery->children as $child)
            @include('admin.galleries._galleries', ['gallery' => $child])
    @endforeach
@endif

</div>
