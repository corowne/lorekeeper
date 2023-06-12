<script>
    $('.pageSelectPopover').popover()

    function onClick(e) {
        const pageCurrent = new URL(window.location.href);
        pageCurrent.searchParams.set("page", e.currentTarget.parentElement.querySelector('.paginationPageRange').value);
        document.location.href = pageCurrent.href;
    }

    $('.pageSelectPopover').on('shown.bs.popover', function() {
        $('.paginator-btn').on('click', onClick);
        // so you can just hit enter after moving the range bar or entering a number
        $('.paginationPageRange').on('keypress', (e) => e.which === 13 && onClick(e));
        $('.paginationPageText').on('keypress', (e) => e.which === 13 ? onClick(e) : true);
    });
</script>
