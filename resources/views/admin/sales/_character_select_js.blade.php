<script>
    $(document).ready(function() {
        var $addCharacter = $('#addCharacter');
        var $components = $('#characterComponents');
        var $characters = $('#characters');
        var count = 0;

        $('#characters .sales-character-entry').each(function(index) {
            attachListeners($(this));
        });

        $addCharacter.on('click', function(e) {
            e.preventDefault();
            $clone = $components.find('.sales-character').clone();
            attachListeners($clone);
            $characters.append($clone);
            count++;
        });

        function attachListeners(node) {
            node.find('.character-code').on('change', function(e) {
                var $parent = $(this).parent().parent().parent().parent();
                $parent.find('.character-image-loaded').load('{{ url('admin/sales/character') }}/'+$(this).val(), function(response, status, xhr) {
                    $parent.find('.character-image-blank').addClass('hide');
                    $parent.find('.character-image-loaded').removeClass('hide');
                    $parent.find('.character-details').removeClass('hide');
                });
            });
            node.find('.remove-character').on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
            attachSaleTypeListener(node.find('.character-sale-type'));
        }

        function attachSaleTypeListener(node) {
            node.on('change', function(e) {
                var val = $(this).val();
                var $cell = $(this).parent().parent().find('.saleType');

                $cell.children().addClass('hide');
                $cell.children().children().val(null);

                if(val == 'auction') {
                    $cell.children('.auctionOptions').addClass('show');
                    $cell.children('.auctionOptions').removeClass('hide');
                    $cell.children('.xtaOptions').addClass('show');
                    $cell.children('.xtaOptions').removeClass('hide');
                }
                else if (val == 'flatsale' || val == 'flaffle'){
                    $cell.children('.flatOptions').addClass('show');
                    $cell.children('.flatOptions').removeClass('hide');
                }
                else if (val == 'ota' || val == 'xta'){
                    $cell.children('.xtaOptions').addClass('show');
                    $cell.children('.xtaOptions').removeClass('hide');
                    $cell.children('.pwywOptions').addClass('show');
                    $cell.children('.pwywOptions').removeClass('hide');
                }
                else if (val == 'pwyw'){
                    $cell.children('.pwywOptions').addClass('show');
                    $cell.children('.pwywOptions').removeClass('hide');
                }
            });
        }

    });
</script>

