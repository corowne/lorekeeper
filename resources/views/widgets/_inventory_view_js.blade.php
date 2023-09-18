<script>
    $(document).ready(function() {

        var $defButton = $('.def-view-button');
        var $defView = $('#defView');
        var $sumButton = $('.sum-view-button');
        var $sumView = $('#sumView');

        var view = null;

        initView();

        $defButton.on('click', function(e) {
            e.preventDefault();
            setView('def');
        });
        $sumButton.on('click', function(e) {
            e.preventDefault();
            setView('sum');
        });

        function initView() {
            view = window.localStorage.getItem('lorekeeper_inventory_view');
            if (!view) view = 'def';
            setView(view);
        }

        function setView(status) {
            view = status;

            if (view == 'def') {
                $defView.removeClass('hide');
                $defButton.addClass('active');
                $sumView.addClass('hide');
                $sumButton.removeClass('active');
                window.localStorage.setItem('lorekeeper_inventory_view', 'def');
            } else if (view == 'sum') {
                $sumView.removeClass('hide');
                $sumButton.addClass('active');
                $defView.addClass('hide');
                $defButton.removeClass('active');
                window.localStorage.setItem('lorekeeper_inventory_view', 'sum');
            }
        }
    });
</script>
