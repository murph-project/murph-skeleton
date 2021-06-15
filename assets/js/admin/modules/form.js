const $ = require('jquery')

module.exports = function () {
  $('body').on('change', '.custom-file-input', function (event) {
    const inputFile = event.currentTarget

    $(inputFile).parent()
      .find('.custom-file-label')
      .html(inputFile.files[0].name)
  })

  $('.nav a').each(function () {
    const link = $(this)
    const href = link.attr('href')

    if (href.substr(0, 1) !== '#') {
      return
    }

    const tab = $('.tab-pane ' + href)

    if (!tab.length) {
      return
    }

    if (tab.find('.form-error-message').length) {
      link.addClass('border border-danger')
      link.click()
    }
  })
}
