const $ = require('jquery')

module.exports = function () {
  $('*[data-dblclick]').dblclick(function (e) {
    document.location.href = $(this).attr('data-dblclick')
  })
}
