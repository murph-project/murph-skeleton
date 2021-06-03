const $ = require('jquery');

const selectedClass = 'table-primary-light';

let toggleRow = (row, checkbox, checkboxIsClicked) => {
    row.toggleClass(selectedClass);

    if (checkboxIsClicked) {
        checkbox.prop('checked', checkbox.prop('checked'));

        return;
    }

    if (checkbox.length) {
        checkbox.prop('checked', !checkbox.prop('checked'));
    }
}

let unactiveRow = (row, checkbox) => {
    row.removeClass(selectedClass);

    if (checkbox.length) {
        checkbox.prop('checked', false);
    }
}

let activeRow = (row, checkbox) => {
    row.addClass(selectedClass);

    if (checkbox.length) {
        checkbox.prop('checked', true);
    }
}

let tableSelectable = () => {
    let tables = $('*[data-selectable]');

    tables.each((i, t) => {
        var table = $(t);
        var rows = table.find('*[data-selectable-row]');
        let selectedIndex = null;

        var tbody = table.find('tbody');

        var resizer = () => {
            tbody.height($(window).height() - tbody.offset().top - 20);
        }

        window.setInterval(resizer, 1000);
        resizer();
        $(window).resize(resizer);


        ((rows) => {
            rows.each((i, r) => {
                let row = $(r);
                let checkbox = row.find('*[data-selectable-checkbox]');
                let selectors = row.find('*[data-selectable-selector]');

                ((row, selectors, checkbox, index) => {
                    selectors.click((e) => {
                        if (event.target.nodeName === 'INPUT') {
                            e.stopPropagation();

                            checkbox.trigger('clicked');

                            return toggleRow(row, checkbox, true);
                        }

                        if (window.event.ctrlKey) {
                            e.preventDefault();

                            return toggleRow(row, checkbox);
                        }

                        if (window.event.button === 0) {
                            if (!window.event.ctrlKey && !window.event.shiftKey) {
                                rows.each((z, r2) => {
                                    unactiveRow($(r2), $(r2).find('*[data-selectable-checkbox]'));
                                });

                                toggleRow(row, checkbox);

                                if (row.hasClass(selectedClass)) {
                                    selectedIndex = index;
                                } else {
                                    selectedIndex = null;
                                }

                                return;
                            }

                            if (window.event.shiftKey) {
                                if (selectedIndex !== null) {
                                    rows.each((z, r2) => {
                                        if (selectedIndex <= index) {
                                            if (z >= selectedIndex && z <= index) {
                                                activeRow($(r2), $(r2).find('*[data-selectable-checkbox]'));
                                            } else {
                                                unactiveRow($(r2), $(r2).find('*[data-selectable-checkbox]'));
                                            }
                                        } else {
                                            if (z <= selectedIndex && z >= index) {
                                                activeRow($(r2), $(r2).find('*[data-selectable-checkbox]'));
                                            } else {
                                                unactiveRow($(r2), $(r2).find('*[data-selectable-checkbox]'));
                                            }
                                        }
                                    });

                                    //selectedIndex = index;
                                }
                            }
                        }
                    });
                })(row, selectors, checkbox, i);
            });
        })(rows);
    });
}

module.exports = tableSelectable;
