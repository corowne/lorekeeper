<script>
    $(document).ready(function() {
        $(".datepicker").datetimepicker({
            dateFormat: "yy-mm-dd",
            timeFormat: 'HH:mm:ss',
            changeMonth: true,
            changeYear: true,
            timezone: '{!! Carbon\Carbon::now()->utcOffset() !!}',
            {!! isset($dtinline) ? "altField: '." . $dtinline . "', altFieldTimeOnly: false," : null !!}
        });
        @if (isset($dtvalue))
            $(".datepicker").datetimepicker("setDate", "{!! Carbon\Carbon::parse($dtvalue) !!} {!! Carbon\Carbon::now()->utcOffset() !!}}");
        @endif
    });
</script>
