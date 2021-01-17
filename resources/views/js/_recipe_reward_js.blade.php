
<script>
$( document ).ready(function() {    
    var $rewardTable  = $('#rewardTableBody');
    var $rewardRow = $('#rewardRow').find('.reward-row');
    var $itemSelect = $('#rewardRowData').find('.item-select');
    var $currencySelect = $('#rewardRowData').find('.currency-select');
    var $tableSelect = $('#rewardRowData').find('.table-select');
    var $raffleSelect = $('#rewardRowData').find('.raffle-select');

    $('#rewardTableBody .selectize').selectize();
    attachRewardTypeListener($('#rewardTableBody .reward-type'));
    attachRemoveListener($('#rewardTableBody .remove-reward-button'));

    $('#addReward').on('click', function(e) {
        e.preventDefault();
        var $clone = $rewardRow.clone();
        $rewardTable.append($clone);
        $clone.find('.selectize').selectize();
        attachRewardTypeListener($clone.find('.reward-type'));
        attachRemoveListener($clone.find('.remove-reward-button'));
    });

    $('.reward-type').on('change', function(e) {
        var val = $(this).val();
        var $cell = $(this).parent().find('.reward-row-select');

        var $clone = null;
        if(val == 'Item') $clone = $itemSelect.clone();
        else if (val == 'Currency') $clone = $currencySelect.clone();
        else if (val == 'LootTable') $clone = $tableSelect.clone();
        else if (val == 'Raffle') $clone = $raffleSelect.clone();

        $cell.html('');
        $cell.append($clone);
    });

    function attachRewardTypeListener(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.reward-row-select');

            var $clone = null;
            if(val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();
            else if (val == 'LootTable') $clone = $tableSelect.clone();
            else if (val == 'Raffle') $clone = $raffleSelect.clone();

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