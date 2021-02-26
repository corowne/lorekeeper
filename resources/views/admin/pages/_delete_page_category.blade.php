@if($category)
    {!! Form::open(['url' => 'admin/page-categories/delete/'.$category->id]) !!}

    <p>You are about to delete the category <strong>{{ $category->name }}</strong>. This is not reversible. If pages in this category exist, you will not be able to delete this category.</p>
    <p>Are you sure you want to delete <strong>{{ $category->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Page Category', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid category selected.
@endif