const $ = require('jquery')

module.exports = function () {
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
