const $ = require('jquery')
const Choices = require('choices.js')

module.exports = function () {
  $('*[data-rest-choices]').each(function (key, item) {
    const url = $(this).attr('data-rest-choices')

    new Choices(item, {
      searchPlaceholderValue: 'Chercher'
    }).setChoices(function () {
      return fetch(url)
        .then(function (response) {
          return response.json()
        })
        .then(function (data) {
          return data.map(function (d) {
            return {
              label: d.label,
              value: d.value
            }
          })
        })
    })
      .then(function (instance) {
      })
  })
}
