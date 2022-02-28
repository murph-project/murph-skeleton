const $ = require('jquery')

const Pannel = () => {
  const panels = $('.panel')

  panels.each((i, p) => {
    const panel = $(p)
    const content = panel.find('.panel-content').first()
    const togglers = panel.find('.panel-toggler')

    togglers.each((k, t) => {
      const toggler = $(t)

      if (!toggler.is('.fa')) {
        return
      }

      if (content.is('.active')) {
        toggler.removeClass('fa-arrow-down')
        toggler.addClass('fa-arrow-up')
      } else {
        toggler.removeClass('fa-arrow-up')
        toggler.addClass('fa-arrow-down')
      }
    })

    togglers.click(function (e) {
      e.stopPropagation()

      content.toggleClass('active')

      togglers.each((k, t) => {
        const toggler = $(t)

        if (!toggler.is('.fa')) {
          return
        }

        toggler
          .toggleClass('fa-arrow-down')
          .toggleClass('fa-arrow-up')
      })
    })
  })
}

module.exports = Pannel
