const $ = require('jquery')

module.exports = function () {
  $('body').on('change', '.custom-file-input', function (event) {
    const inputFile = event.currentTarget

    $(inputFile).parent()
      .find('.custom-file-label')
      .html(inputFile.files[0].name)
  })
}
