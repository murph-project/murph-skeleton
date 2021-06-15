const $ = require('jquery')

module.exports = function () {
  $('.toast').toast({
    animation: true,
    autohide: true,
    delay: 5000
  })

  $('.toast').toast('show')
}
