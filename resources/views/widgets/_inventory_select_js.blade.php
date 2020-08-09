<script>
    $(document).ready(function() {
        var $userItemCategory = $('#userItemCategory');
        $userItemCategory.on('change', function(e) {
            refreshCategory();
        });
        $('.inventory-select-all').on('click', function(e) {
            e.preventDefault();
            selectVisible();
        });
        $('.inventory-clear-selection').on('click', function(e) {
            e.preventDefault();
            deselectVisible();
        });
        $('.inventory-checkbox').on('change', function() {
            $checkbox = $(this);
            var rowId = "#itemRow" + $checkbox.val()
            if($checkbox.is(":checked")) {
                $(rowId).addClass('category-selected');
                $(rowId).find('.quantity-select').prop('name', 'stack_quantity[]')
            }
            else {
                $(rowId).removeClass('category-selected');
                $(rowId).find('.quantity-select').prop('name', '')
            }
        });
        $('#toggle-checks').on('click', function() {
            ($(this).is(":checked")) ? selectVisible() : deselectVisible();
        });
        
        function refreshCategory() {
            var display = $userItemCategory.val();
            $('.user-item').addClass('hide');
            $('.user-items .category-' + display).removeClass('hide');
            $('#toggle-checks').prop('checked', false);
        }
        function selectVisible() {
            var $target = $('.user-item:not(.hide)');
            $target.addClass('category-selected');
            $target.find('.inventory-checkbox').prop('checked', true);
            $('#toggle-checks').prop('checked', true);
            $target.find('.quantity-select').prop('name', 'stack_quantity[]');
        }
        function deselectVisible() {
            var $target = $('.user-item:not(.hide)');
            $target.removeClass('category-selected');
            $target.find('.inventory-checkbox').prop('checked', false);
            $('#toggle-checks').prop('checked', false);
            $target.find('.quantity-select').prop('name', '');
        }
    });
</script>