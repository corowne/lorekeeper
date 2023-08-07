<script>
    $(document).ready(function() {
        var $addCharacter = $('#addCharacter');
        var $components = $('#characterComponents');
        var $rewards = $('#rewards');
        var $characters = $('#characters');
        var count = 0;

        $('#characters .submission-character').each(function(index) {
            attachListeners($(this));
        });

        $addCharacter.on('click', function(e) {
            e.preventDefault();
            $clone = $components.find('.submission-character').clone();
            attachListeners($clone);
            attachRewardTypeListener($clone.find('.character-rewardable-type'));
            $characters.append($clone);
            count++;
        });

        function attachListeners(node) {
            node.find('.character-code').on('input', function(e) {
                var $parent = $(this).parent().parent().parent().parent();
                $parent.find('.character-image-loaded').load('{{ url('submissions/new/character') }}/'+$(this).val(), function(response, status, xhr) {
                    $parent.find('.character-image-blank').addClass('hide');
                    $parent.find('.character-image-loaded').removeClass('hide');
                    $parent.find('.character-rewards').removeClass('hide');
                    updateRewardNames(node, node.find('.character-info').data('id'));
                });
            });
            node.find('.remove-character').on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
            node.find('.add-reward').on('click', function(e) {
                e.preventDefault();
                $clone = $components.find('.character-reward-row').clone();
                $clone.find('.remove-reward').on('click', function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
                updateRewardNames($clone, node.find('.character-info').data('id'));
                $(this).parent().parent().find('.character-rewards').append($clone);
            });
        }


        $('.character-rewardable-type').on('change', function(e) {
                console.log('hello');
            var val = $(this).val();
            var $cell = $(this).parent().find('.character-loot-row-select');
                console.log($cell);

            var $clone = null;
            if(val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();
            @if(isset($showLootTables) && $showLootTables)
                else if (val == 'LootTable') $clone = $tableSelect.clone();
            @endif

            $cell.html('');
            $cell.append($clone);
        });

        function attachRewardTypeListener(node) {
            node.on('change', function(e) {
                console.log($(this));
                console.log('hello');
                var val = $(this).val();
                var $cell = $(this).parent().parent().find('.character-loot-row-select');

                var $clone = null;
                if(val == 'Item') $clone = $itemSelect.clone();
                else if (val == 'Currency') $clone = $currencySelect.clone();
                @if(isset($showLootTables) && $showLootTables)
                    else if (val == 'LootTable') $clone = $tableSelect.clone();
                @endif

                $cell.html('');
                $cell.append($clone);
                $clone.selectize();
            });
        }

        function updateOptions() {
            if(flat) $('#flatOptions').removeClass('hide');
            else $('#flatOptions').addClass('hide');

            if(range) $('#rangeOptions').removeClass('hide');
            else $('#rangeOptions').addClass('hide');

            if(min) $('#minOptions').removeClass('hide');
            else $('#minOptions').addClass('hide');

            if(rate) $('#rateOptions').removeClass('hide');
            else $('#rateOptions').addClass('hide');
        }

        function updateRewardNames(node, id) {
            node.find('.character-rewardable-type').attr('name', 'character_rewardable_type[' + id + '][]');
            node.find('.character-rewardable-quantity').attr('name', 'character_rewardable_quantity[' + id + '][]');
            node.find('.character-currency-id').attr('name', 'character_currency_id[' + id + '][]');
            node.find('.character-item-id').attr('name', 'character_item_id[' + id + '][]');
            node.find('.character-table-id').attr('name', 'character_table_id[' + id + '][]');
        }

    });
</script>
