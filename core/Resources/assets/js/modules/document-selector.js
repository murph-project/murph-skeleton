const $ = require('jquery')

const DocumentSelector = () => {
  const forms = $('.document-selector-form')

  const handler = function () {
    forms.each((fi, f) => {
      const form = $(f)
      const ids = form.find('.document-selector-ids')
      const btn = form.find('.document-selector-button')

      ids.html('')
      let hasSelection = false

      $('*[data-documents] *[data-selectable-row] input[data-selectable-checkbox]').each((i, c) => {
        const checkbox = $(c)

        if (checkbox.is(':checked')) {
          ids.append(checkbox[0].outerHTML)
          hasSelection = true
        }
      })

      if (hasSelection && btn.length) {
        btn.removeAttr('disabled')
        ids.find('input').prop('checked', true)
      } else {
        btn.attr('disabled', 'disabled')
      }
    })
  }

  $('*[data-documents] *[data-selectable-row]').click(function () {
    window.setTimeout(handler, 100)
  })

  $('*[data-documents] *[data-selectable-row]').on('clicked', function () {
    window.setTimeout(handler, 100)
  })
}

module.exports = DocumentSelector
