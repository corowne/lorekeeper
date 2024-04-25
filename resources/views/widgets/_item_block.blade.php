 {!! Form::open(['url' => 'account/image-blocks/block/' . $item->id]) !!}
 @php
     $model = get_class($item);
 @endphp
 <input type="hidden" name="item_type" value="{{ $model }}" />
 {!! Form::submit(checkItemBlock($item, Auth::user()) ? 'Unblock' : 'Block', ['class' => 'btn btn-success btn-sm']) !!}
 {!! Form::close() !!}
