<script>
var pageSelectPopoverShown{{ $instanceID }} = false;
var pageSelectRange{{ $instanceID }} = new Object();
var pageSelectText{{ $instanceID }} = new Object();

var pageCurrent{{ $instanceID }} = new URL(window.location.href);

$(function () {
    $('.pageSelectPopover{{ $instanceID }}').popover()
});

$('.pageSelectPopover{{ $instanceID }}').on('shown.bs.popover', function () {
	pageWorkingPopover{{ $instanceID }} = this;
    pageSelectPopoverShown{{ $instanceID }} = true;
    pageSelectRange{{ $instanceID }} = document.getElementById('paginationPageRange{{ $instanceID }}');
    pageSelectText{{ $instanceID }} = document.getElementById('paginationPageText{{ $instanceID }}');
});

$('.pageSelectPopover{{ $instanceID }}').on('hidden.bs.popover', function () {
    pageWorkingPopover{{ $instanceID }} = null;
	pageSelectPopoverShown{{ $instanceID }} = false;
    pageSelectRange{{ $instanceID }} = null;
    pageSelectText{{ $instanceID }} = null;
});

function pageUpdateSelectText{{ $instanceID }}() {
    pageSelectText{{ $instanceID }}.value = pageSelectRange{{ $instanceID }}.value;
}

function pageUpdateSelectRange{{ $instanceID }}() {
    pageSelectRange{{ $instanceID }}.value = pageSelectText{{ $instanceID }}.value;
}

function pageGo{{ $instanceID }}() {
	pageCurrent{{ $instanceID }}.searchParams.set("page", pageSelectRange{{ $instanceID }}.value);
	 document.location.href = pageCurrent{{ $instanceID }}.href;
}
</script>