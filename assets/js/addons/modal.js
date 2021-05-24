const $ = require('jquery');

module.exports = function() {
    $('body').on('click', '*[data-modal]', (e) => {
        e.preventDefault();
        e.stopPropagation();

        let container = $('#modal-container');
        const body = $('body')

        if (!container.length) {
            container = $('<div id="modal-container" class="modal">');

            body.append(container);
        }

        const loader = $('<div style="position: absolute; top: 25vh; left: 50vw; z-index: 2000">')
        loader.html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>')
        body.append(loader)

        container.html();

        const url = $(e.target).attr('data-modal');
            $(container).modal('show');

        container.load(url, function() {
            loader.remove()
        });
    });

    const urlParams = new URLSearchParams(window.location.search)
    const dataModal = urlParams.get('data-modal')

    if (dataModal) {
        $('*[data-modal="' + dataModal + '"]').first().click();
    }
}
