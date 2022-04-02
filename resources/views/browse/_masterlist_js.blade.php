<script>
    $(document).ready(function() {
        $('.userselectize').selectize();

        var $gridButton = $('.grid-view-button');
        var $gridView = $('#gridView');
        var $listButton = $('.list-view-button');
        var $listView = $('#listView');

        var view = null;

        initView();

        $gridButton.on('click', function(e) {
            e.preventDefault();
            setView('grid');
        });
        $listButton.on('click', function(e) {
            e.preventDefault();
            setView('list');
        });

        function initView()
        {
            view = window.localStorage.getItem('lorekeeper_masterlist_view');
            if(!view) view = 'grid';
            setView(view);
        }

        function setView(status)
        {
            view = status;

            if(view == 'grid') {
                $gridView.removeClass('hide');
                $gridButton.addClass('active');
                $listView.addClass('hide');
                $listButton.removeClass('active');
                window.localStorage.setItem('lorekeeper_masterlist_view', 'grid');
            }
            else if (view == 'list') {
                $listView.removeClass('hide');
                $listButton.addClass('active');
                $gridView.addClass('hide');
                $gridButton.removeClass('active');
                window.localStorage.setItem('lorekeeper_masterlist_view', 'list');
            }
        }

        var $featureBody = $('#featureBody');
        var $featureSelect = $('#featureContent .feature-block');
        var $addFeatureButton = $('.add-feature-button');

        // handle the ones that were already there
        var $existingFeatures = $('#featureBody .feature-block');
        @if(Config::get('lorekeeper.extensions.organised_traits_dropdown'))
            $existingFeatures.find('.selectize').selectize({
                render: {
                    item: featureSelectedRender
                }
            });
        @else
            $existingFeatures.find('.selectize').selectize();
        @endif
        addRemoveListener($existingFeatures);

        $addFeatureButton.on('click', function(e) {
            e.preventDefault();
            var $clone = $featureSelect.clone();
            $featureBody.append($clone);
            @if(Config::get('lorekeeper.extensions.organised_traits_dropdown'))
                $clone.find('.selectize').selectize({
                    render: {
                        item: featureSelectedRender
                    }
                });
            @else
                $clone.find('.selectize').selectize();
            @endif
            addRemoveListener($clone);
        });

        function featureSelectedRender(item, escape) {
            return '<div><span>' + escape(item["text"].trim()) + ' (' + escape(item["optgroup"].trim()) + ')' + '</span></div>';
        }

        function addRemoveListener($node)
        {
            $node.find('.feature-remove').on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
        }
    });
</script>
