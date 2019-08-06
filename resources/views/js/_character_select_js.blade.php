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
            $characters.append($clone);
            count++;
        });

        function attachListeners(node) {
            node.find('.character-code').on('change', function(e) {
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

        function updateRewardNames(node, id) {
            node.find('.currency-id').attr('name', 'character_currency_id[' + id + '][]');
            node.find('.quantity').attr('name', 'character_quantity[' + id + '][]');
        }

    });
</script>