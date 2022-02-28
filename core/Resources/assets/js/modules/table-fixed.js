const $ = require('jquery')

const resizeTbody = (tbody) => {
  tbody.height($(window).height() - tbody.offset().top - 20)
}

const tableFixed = () => {
  const tables = $('table[data-table-fixed], *[data-table-fixed] > table')

  tables.each((i, t) => {
    const table = $(t)
    table.addClass('table-fixed')

    const tbody = table.find('tbody')

    resizeTbody(tbody)

    $(window).resize(function () {
      resizeTbody(tbody)
    })
  })
}

module.exports = tableFixed
