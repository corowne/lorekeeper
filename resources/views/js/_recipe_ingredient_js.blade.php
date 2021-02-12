<script>
$( document ).ready(function() {
    var $ingredientTable  = $('#ingredientTableBody');
    var $ingredientRow = $('#ingredientRow').find('.ingredient-row');
    var $itemSelect = $('#ingredientRowData').find('.item-select');
    var $multiItemSelectGroup = $('#ingredientRowData').find('.multi-item-select-group');
    var $multiItemEntry = $('#ingredientRowData').find('.multi-item-entry');
    var $categorySelect = $('#ingredientRowData').find('.category-select');
    var $multiCategorySelectGroup = $('#ingredientRowData').find('.multi-category-select-group');
    var $multiCategoryEntry = $('#ingredientRowData').find('.multi-category-entry');
    var $currencySelect = $('#ingredientRowData').find('.currency-select');
    var currentRowID = $ingredientTable.find('.ingredient-row').length;

    $('#ingredientTableBody .selectize').selectize();
    attachIngredientTypeListener($('#ingredientTableBody .ingredient-type'));
    attachRemoveListener($('#ingredientTableBody .remove-ingredient-button'));

    $('#addIngredient').on('click', function(e) {
        e.preventDefault();
        var $clone = $ingredientRow.clone();
        $clone = $clone.attr('data-row', currentRowID);
        $ingredientTable.append($clone);
        var $typeSelect = $clone.find('.ingredient-type');
        $typeSelect.attr('name', 'ingredient_type['+currentRowID+']')
        $clone.find('.ingredient-quantity').attr('name', 'ingredient_quantity['+currentRowID+']')
        attachIngredientTypeListener($typeSelect);
        attachRemoveListener($clone.find('.remove-ingredient-button'));
        currentRowID++;
    });

    attachAddMultiListener($('#ingredientTableBody .add-multi-item-button'), 'MultiItem');
    attachAddMultiListener($('#ingredientTableBody .add-multi-category-button'), 'MultiCategory');
    attachRemoveMultiListener($('#ingredientTableBody .remove-multi-entry-button'));

    function attachIngredientTypeListener(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.ingredient-row-select');
            var row_num = $(this).parent().parent().data('row');

            var $clone = null;
            if(val == 'Item') $clone = $itemSelect.clone();
            else if(val == 'MultiItem') {
                $clone = $multiItemSelectGroup.clone();
                attachAddMultiListener($clone.find('.add-multi-item-button'), val);
                attachRemoveMultiListener($clone.find('.remove-multi-entry-button'));
            }
            else if(val == 'Category') $clone = $categorySelect.clone();
            else if(val == 'MultiCategory') {
                $clone = $multiCategorySelectGroup.clone();
                attachAddMultiListener($clone.find('.add-multi-category-button'), val);
                attachRemoveMultiListener($clone.find('.remove-multi-entry-button'));
            }
            else if (val == 'Currency') $clone = $currencySelect.clone();

            $cell.html('');
            $cell.append($clone);
            var $select;
            if(val == 'MultiItem') $select = $clone.find('.multi-item-select');
            else if(val == 'MultiCategory') $select = $clone.find('.multi-category-select');
            else $select = $clone;

            $select.attr('name', 'ingredient_data['+row_num+'][]');
            $select.selectize();
        });
    }

    function attachRemoveListener(node) {
        node.on('click', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
    }

    function attachAddMultiListener(node, type) {
        // Add listener to the add item/category buttons on multi item and multi category
        node.on('click', function(e) {
            e.preventDefault();
            var $clone;
            var $select;
            var row_num = $(this).parent().parent().parent().data('row');
            if(type == 'MultiItem') {
                $clone = $multiItemEntry.clone();
                $(this).parent().find('.multi-item-list').append($clone);
                $select = $clone.find('.multi-item-select');
            }
            else if(type == 'MultiCategory') {
                $clone = $multiCategoryEntry.clone();
                $(this).parent().find('.multi-category-list').append($clone);
                $select = $clone.find('.multi-category-select');
            }
            $select.attr('name', 'ingredient_data['+row_num+'][]');
            $select.selectize();
            attachRemoveMultiListener($clone.find('.remove-multi-entry-button'));
            updateRemoveMulti($(this).parent());
        });
    }

    function attachRemoveMultiListener(node) {
        // Add listener to the remove item/category button per entry
        node.on('click', function(e) {
            e.preventDefault();
            $parent = $(this).parent().parent().parent();
            $(this).parent().parent().remove();
            updateRemoveMulti($parent);
        });
    }

    function updateRemoveMulti(node) {
        // Check the node for all instances of the remove entry button
        // Hides the button if there is only one
        var $buttons = node.find('.remove-multi-entry-button');
        if($buttons.length == 1) $buttons.addClass('hide');
        else $buttons.removeClass('hide');
    }
});
    
</script>