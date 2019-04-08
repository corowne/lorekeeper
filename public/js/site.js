function loadModal(url, title) {
    $('#modal').find('.modal-body').html('');
    $('#modal').find('.modal-header').html(title);
    $('#modal').find('.modal-body').load(url);
    $('#modal').modal('show');
}