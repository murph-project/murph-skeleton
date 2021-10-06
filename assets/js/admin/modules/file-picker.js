const $ = require('jquery')
const Vue = require('vue').default
const FileManager = require('../components/file-manager/FileManager').default

const createModal = function () {
  let container = $('#fm-modal')
  const body = $('body')

  if (!container.length) {
    container = $('<div id="fm-modal" class="modal">')

    body.append(container)
  }

  container.html(`
<div class="modal-dialog modal-dialog-large">
    <div class="modal-content">
        <div class="modal-body">
            <div id="fm-modal-content">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
`)

  $(container).modal('show')

  return $(container)
}

const fileManagerBrowser = function (callback) {
  const container = createModal()

  const clickCallback = (e) => {
    callback($(e.target).attr('data-value'), {})
    $('#modal-container').modal('hide')
    container.modal('hide')

    $('body').off('click', '#file-manager-insert', clickCallback)
  }

  $('body').on('click', '#file-manager-insert', clickCallback)

  return new Vue({
    el: '#fm-modal-content',
    template: '<FileManager context="tinymce" />',
    components: {
      FileManager
    }
  })
}

module.exports = function () {
  $('body').on('click', '.form-filepicker-picker', (e) => {
    e.preventDefault()

    const picker = $(e.target)
    const id = '#' + picker.attr('data-target')
    const input = $(id)

    fileManagerBrowser((value) => {
      value = value.replace(/^\//, '')

      picker.parents('.form-filepicker-container').find('input.form-filepicker-picker').val(value)
      input.val(value)
    })
  })

  $('body').on('click', '.form-filepicker-reset', (e) => {
    e.preventDefault()

    const button = $(e.target)
    const id = '#' + button.attr('data-target')
    const input = $(id)

    input.val('')
    button.parents('.form-filepicker-container').find('input.form-filepicker-picker').val('')
  })
}
