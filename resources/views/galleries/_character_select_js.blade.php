<script>
    $(document).ready(function() {
        var $addCharacter = $('#addCharacter');
        var $components = $('#characterComponents');
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
            $clone.find('.character-code').selectize();
            count++;
        });

        function attachListeners(node) {
            node.find('.character-code').on('change', function(e) {
                var $parent = $(this).parent().parent().parent().parent();
                $parent.find('.character-image-loaded').load('{{ url('gallery/submit/character') }}/' + $(this).val(), function(response, status, xhr) {
                    $parent.find('.character-image-blank').addClass('hide');
                    $parent.find('.character-image-loaded').removeClass('hide');
                    $parent.find('.character-rewards').removeClass('hide');
                });
            });
            node.find('.remove-character').on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
        }

    });
</script>
