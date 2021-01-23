
<script>
$( document ).ready(function() {    
    var $limitTable  = $('#limitTableBody');
    var $limitRow = $('#limitRow').find('.limit-row');
    var $itemSelect = $('#limitRowData').find('.item-select');
    var $currencySelect = $('#limitRowData').find('.currency-select');
    var $recipeSelect = $('#limitRowData').find('.recipe-select');


    $('#limitTableBody .selectize').selectize();
    attachRewardTypeListener($('#limitTableBody .reward-type'));
    attachRemoveListener($('#limitTableBody .remove-limit-button'));

    $('#addLimit').on('click', function(e) {
        e.preventDefault();
        var $clone = $limitRow.clone();
        $limitTable.append($clone);
        attachRewardTypeListener($clone.find('.reward-type'));
        attachRemoveListener($clone.find('.remove-limit-button'));
    });

    $('.reward-type').on('change', function(e) {
        var val = $(this).val();
        var $cell = $(this).parent().find('.limit-row-select');

        var $clone = null;
        if(val == 'Item') $clone = $itemSelect.clone();
        else if (val == 'Currency') $clone = $currencySelect.clone();
        else if (val == 'Recipe') $clone = $recipeSelect.clone();

        $cell.html('');
        $cell.append($clone);
    });

    function attachRewardTypeListener(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.limit-row-select');

            var $clone = null;
            if(val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();
            else if (val == 'Recipe') $clone = $recipeSelect.clone();

            $cell.html('');
            $cell.append($clone);
            $clone.selectize();
        });
    }

    function attachRemoveListener(node) {
        node.on('click', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
    }

});
    
</script>