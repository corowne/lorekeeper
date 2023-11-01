<div class="p-2">
    {!! Form::open(['url' => 'user-shops/stock/edit/'.$stock->id]) !!}

    <div class="form-group">
        {!! Form::label('cost', 'Cost') !!}
        <div class="row">
            <div class="col-4">
                {!! Form::text('cost', $stock->cost ?? null, ['class' => 'form-control stock-field', 'data-name' => 'cost']) !!}
            </div>
            <div class="col-8">
                {!! Form::select('currency_id', $currencies, $stock->currency_id ?? null, ['class' => 'form-control stock-field', 'data-name' => 'currency_id']) !!}
            </div>
        </div>
    </div>
        <div class="form-group">
            {!! Form::checkbox('is_visible', 1, $stock->is_visible ?? 1, ['class' => 'form-check-input stock-limited stock-toggle stock-field']) !!}
            {!! Form::label('is_visible', 'Set Visibility', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off it will not appear in the store.') !!}
        </div>
    </div>


<div class="text-right mt-1">
    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}
</div>
<script>
    $(document).ready(function() {
        $('#type').change(function() {
            var type = $(this).val();
            $.ajax({
            type: "GET", url: "{{ url('user-shops/stock/stock-type') }}?type="+type, dataType: "text"
            }).done(function (res) { $("#stock").html(res); }).fail(function (jqXHR, textStatus, errorThrown) { alert("AJAX call failed: " + textStatus + ", " + errorThrown); });
        }); 
    });
</script>