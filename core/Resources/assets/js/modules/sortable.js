const $ = require('jquery')
const Sortable = require('sortablejs').Sortable

module.exports = () => {
  $('*[data-sortable]').each((i, list) => {
    const element = $(list)
    const route = element.attr('data-sortable-route')

    return new Sortable(list, {
      handle: '*[data-sortable-item]',
      sort: true,
      animation: 150,
      fallbackTolerance: 3,
      onEnd: (e) => {
        if (!route) {
          return
        }

        const items = element.find('*[data-sortable-item]')
        const datas = { items: [] }

        items.each((order, v) => {
          datas.items[$(v).attr('data-sortable-item')] = order + 1
        })

        $.post(route, datas)
          .always((data) => {
            document.location.reload()
          })
      }
    })
  })
}
