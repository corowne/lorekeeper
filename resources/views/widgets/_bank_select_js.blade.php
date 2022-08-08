<script>
    $(document).ready(function() {
        $('.currency-table .selectize').selectize();
        attachRemoveListener($('.currency-table .remove-currency-button'));

        $('.add-currency-button').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var $clone = $('#' + $this.data('type') + 'Row-' + $this.data('id')).find('.bank-row').clone();
            $clone.find('.selectize').selectize();
            console.log($clone);
            $('#' + $this.data('type') + 'Body-' + $this.data('id')).append($clone);
            attachRemoveListener($clone.find('.remove-currency-button'));
        });

        function attachRemoveListener(node) {
            node.on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
        }
    });
</script>
