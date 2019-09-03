<script>
    $(document).ready(function() {
        var $userItemCategory = $('#userItemCategory');
        $userItemCategory.on('change', function(e) {
            refreshCategory();
        });
        $('.inventory-stack').on('click', function(e) {
            var $parent = $(this).parent().parent().parent();
            $parent.toggleClass('category-selected');
            $parent.find('.inventory-checkbox').prop('checked', $parent.hasClass('category-selected'));
            refreshCategory();
        });
        $('.inventory-info').on('click', function(e) {
            e.preventDefault();
            var $parent = $(this).parent().parent().parent();
            loadModal("{{ url('items') }}/" + $parent.data('id') + "?read_only={{ isset($readOnly) && $readOnly ? 1 : 0 }}", $parent.data('name'));
        });
        $('.inventory-select-all').on('click', function(e) {
            e.preventDefault();
            var $target = $('.user-item:not(.hide)');
            $target.addClass('category-selected');
            $target.find('.inventory-checkbox').prop('checked', true);
        });
        $('.inventory-clear-selection').on('click', function(e) {
            e.preventDefault();
            var $target = $('.user-item:not(.hide)');
            $target.removeClass('category-selected');
            $target.find('.inventory-checkbox').prop('checked', false);
        });

        function refreshCategory() {
            var display = $userItemCategory.val();
            $('.user-item').addClass('hide');
            $('.category-' + display).removeClass('hide');
        }
    });
</script>