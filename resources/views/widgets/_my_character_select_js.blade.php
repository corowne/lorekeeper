<script>
    $(document).ready(function() {
        var $userCharacterCategory = $('#userCharacterCategory');
        $userCharacterCategory.on('change', function(e) {
            refreshCharacterCategory();
        });
        $('.character-stack').on('click', function(e) {
            if (!$(this).parent().parent().hasClass('disabled')) {
                var $parent = $(this).parent().parent().parent();
                $parent.toggleClass('category-selected');
                $parent.find('.character-checkbox').prop('checked', $parent.hasClass('category-selected'));
                refreshCharacterCategory();
            }
        });
        $('.characters-select-all').on('click', function(e) {
            e.preventDefault();
            var $target = $('.user-character:not(.hide):not(.select-disabled)');
            $target.addClass('category-selected');
            $target.find('.character-checkbox').prop('checked', true);
        });
        $('.characters-clear-selection').on('click', function(e) {
            e.preventDefault();
            var $target = $('.user-character:not(.hide)');
            $target.removeClass('category-selected');
            $target.find('.character-checkbox').prop('checked', false);
        });

        function refreshCharacterCategory() {
            var display = $userCharacterCategory.val();
            $('.user-character').addClass('hide');
            $('.user-characters .category-' + display).removeClass('hide');
        }
    });
</script>
