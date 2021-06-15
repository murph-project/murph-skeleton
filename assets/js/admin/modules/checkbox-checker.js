const $ = require('jquery')

module.exports = function () {
  $('*[data-checkbox-ckecker]').click(function () {
    const wrapperName = $(this).attr('data-checkbox-ckecker')

    if (!wrapperName) {
      return
    }

    const checkboxes = $('*[data-checkbox-wrapper="' + wrapperName + '"] *[data-checkbox] input[type="checkbox"]')

    $(checkboxes).each(function (i, v) {
      $(v).prop('checked', true)
    })
  })

  $('*[data-checkbox-unckecker]').click(function () {
    const wrapperName = $(this).attr('data-checkbox-unckecker')

    if (!wrapperName) {
      return
    }

    const checkboxes = $('*[data-checkbox-wrapper="' + wrapperName + '"] *[data-checkbox] input[type="checkbox"]')

    $(checkboxes).each(function (i, v) {
      $(v).prop('checked', false)
    })
  })
}
