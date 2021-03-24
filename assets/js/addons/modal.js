const $ = require('jquery');

module.exports = function() {
    $('body').on('click', '*[data-modal]', (e) => {
        e.preventDefault();
        e.stopPropagation();

        let container = $('#modal-container');

        if (!container.length) {
            container = $('<div id="modal-container" class="modal">');

            $('body').append(container);
        }

        container.html('');

        const url = $(e.target).attr('data-modal');

        container.load(url, function() {
            $(container).modal('show');
        });
    });

    const urlParams = new URLSearchParams(window.location.search)
    const dataModal = urlParams.get('data-modal')

    if (dataModal) {
        $('*[data-modal="' + dataModal + '"]').first().click();
    }
}
