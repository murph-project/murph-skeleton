const $ = require('jquery');

let resizeTbody = (tbody) => {
    tbody.height($(window).height() - tbody.offset().top - 20);
}

let tableFixed = () => {
    let tables = $('table[data-table-fixed], *[data-table-fixed] > table');

    tables.each((i, t) => {
        let table = $(t);
        table.addClass('table-fixed');

        let tbody = table.find('tbody');

        resizeTbody(tbody);

        $(window).resize(function() {
            resizeTbody(tbody);
        });
    });
}

module.exports = tableFixed;
