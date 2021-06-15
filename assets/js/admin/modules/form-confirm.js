const $ = require('jquery')

module.exports = function () {
  $('body').on('submit', '*[data-form-confirm]', function (e) {
    let message = $(this).attr('data-form-confirm')

    if (!message) {
      message = 'Confimez-vous cette action ?'
    }

    if (!confirm(message)) {
      e.preventDefault()
    }
  })
}
