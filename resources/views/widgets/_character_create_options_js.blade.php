<script>
    $(document).ready(function() {
        $('#userSelect').selectize();
        // Resell options /////////////////////////////////////////////////////////////////////////////

        var $resellable = $('#resellable');
        var $resellOptions = $('#resellOptions');

        var resellable = $resellable.is(':checked');

        updateOptions();

        $resellable.on('change', function(e) {
            resellable = $resellable.is(':checked');

            updateOptions();
        });

        function updateOptions() {
            if (resellable) $resellOptions.removeClass('hide');
            else $resellOptions.addClass('hide');
        }
    });
</script>
