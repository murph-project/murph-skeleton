const $ = require('jquery')

module.exports = function () {
  $('body').on('submit', '*[data-form-ajax]', function (e) {
    e.preventDefault()

    const target = e.target
    const form = $(target)
    const data = new FormData(target)
    const method = form.attr('method')
    const files = form.find('input[type=file]')

    files.each((i, v) => {
      data.append(v.name, v)
    })

    const options = {
      url: form.attr('action'),
      data: data,
      processData: false,
      contentType: false,
      type: method || 'GET',
      success: function (data) {
        if (Object.prototype.hasOwnProperty.call(data, '_dispatch')) {
          $('body').trigger(data._dispatch)
        }

        if (Object.prototype.hasOwnProperty.call(data, '_message') && Object.prototype.hasOwnProperty.call(data, '_level')) {
          const message = data._message
          const level = data._level
          const titles = {
            notice: 'Information',
            info: 'Information',
            success: 'Success',
            warning: 'Warning',
            danger: 'Danger',
            error: 'Error'
          }
          const borders = {
            notice: '',
            info: 'border border-primary',
            success: 'border border-success',
            warning: 'border border-warning',
            danger: 'border border-danger',
            error: 'border border-danger'
          }
          const colors = {
            info: 'text-body',
            notice: 'text-body',
            success: 'text-success font-weight-bold',
            warning: 'text-warning font-weight-bold',
            danger: 'text-danger font-weight-bold',
            error: 'text-danger font-weight-bold'
          }

          $('#toast-wrapper-main').append(
            `
                <div class="toast ${borders[level]}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="mr-auto">${titles[level]}</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body text-${colors[level]}">
                        ${message}
                    </div>
                </div>
            `
          )

          $('.toast').last().toast('show')
        }
      }
    }

    $.ajax(options)
  })
}
