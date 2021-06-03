const $ = require('jquery');

module.exports = function() {
    $('*[data-form-confirm]').submit(function(e) {
        let message = $(this).attr('data-form-confirm');

        if (!message) {
            message = 'Confimez-vous cette action ?';
        }

        if (!confirm(message)) {
            e.preventDefault();
        }
    })
};
