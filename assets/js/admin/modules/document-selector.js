const $ = require('jquery');

let DocumentSelector = () => {
    let forms = $('.document-selector-form');
    let btnSubmit = $('#download-archive-form button');

    let handler = function() {
        forms.each((fi, f) => {
            let form = $(f);
            let ids = form.find('.document-selector-ids');
            let btn = form.find('.document-selector-button');

            ids.html('');
            let hasSelection = false;

            $('*[data-documents] *[data-selectable-row] input[data-selectable-checkbox]').each((i, c) => {
                let checkbox = $(c);

                if (checkbox.is(':checked')) {
                    ids.append(checkbox[0].outerHTML);
                    hasSelection = true;
                }
            });

            if (hasSelection && btn.length) {
                btn.removeAttr('disabled');
                ids.find('input').prop('checked', true);
            } else {
                btn.attr('disabled', 'disabled');
            }
        })
    }

    $('*[data-documents] *[data-selectable-row]').click(function() {
        window.setTimeout(handler, 100)
    });

    $('*[data-documents] *[data-selectable-row]').on('clicked', function() {
        window.setTimeout(handler, 100)
    });
}

module.exports = DocumentSelector;
