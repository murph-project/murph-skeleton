const $ = require('jquery')

module.exports = () => {
  $('th.crud-batch-column input').change((e) => {
    $('td.crud-batch-column input').prop('checked', $(e.target).is(':checked'))
  })

  const form = $('#form-batch')

  form.submit((e) => {
    e.preventDefault()

    const route = form.attr('action')
    const datas = form.serialize()

    form.addClass('is-loading')

    $.post(route, datas)
      .always(() => {
        document.location.reload()
      })
  })
}
