const $ = require('jquery')

module.exports = function () {
  $('*[data-pushstate]').click((e) => {
    const url = $(e.target).attr('data-pushstate')

    history.pushState({ url: url }, null, url)
    history.replaceState({ url: url }, null, url)
  })

  const forms = $('form[data-formpushstate]')

  const checkAndUsePushState = () => {
    const state = [window.location.pathname, window.location.search].join('')

    $('*[data-pushstate]').each((i, v) => {
      let method = 'compare'

      if ($(v).is('[data-pushstate-method]')) {
        method = $(v).attr('data-pushstate-method')
      }

      let isThisOne = false

      if (method === 'compare' && $(v).attr('data-pushstate') === state) {
        isThisOne = true
      }

      if (method === 'indexOf' && state.indexOf($(v).attr('data-pushstate')) !== -1) {
        isThisOne = true
      }

      if (isThisOne) {
        $(v).click()

        forms.attr('action', state)
      }
    })
  }

  checkAndUsePushState()

  $(window).on('statechange', checkAndUsePushState, false)
}
