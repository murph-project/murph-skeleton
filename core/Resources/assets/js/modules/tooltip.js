const $ = require('jquery')

module.exports = function () {
  $('*[data-toggle="tooltip"]').tooltip()
}
