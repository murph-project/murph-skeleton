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

if (typeof window.tinymce !== 'undefined') {
  window.tinymce.murph = window.tinymce.murph || {}
  window.tinymce.murph.selector = window.tinymce.murph.selector || '*[data-tinymce]'
  window.tinymce.murph.configurationBase = window.tinymce.murph.configurationBase || {
    base_url: '/vendor/tinymce/',
    cache_suffix: '?v=4.1.6',
    importcss_append: true,
    image_caption: true,
    noneditable_noneditable_class: 'mceNonEditable',
    toolbar_drawer: 'sliding',
    spellchecker_dialog: true,
    tinycomments_mode: 'embedded',
    convert_urls: false,
    file_picker_callback: fileManagerBrowser,
    file_picker_types: 'image',
    init_instance_callback: function (editor) {
      editor.on('SetContent', () => {
        window.tinymce.triggerSave(false, true)
      })

      editor.on('Change', () => {
        window.tinymce.triggerSave(false, true)
      })
    }
  }

  window.tinymce.murph.modes = window.tinymce.murph.modes || {}

  window.tinymce.murph.modes.default = window.tinymce.murph.modes.default || {
    plugins: 'print preview importcss searchreplace visualblocks visualchars fullscreen template table charmap hr pagebreak nonbreaking toc insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars link image code autoresize',
    menubar: 'file edit view insert format tools table tc help',
    toolbar: 'undo redo | bold italic underline strikethrough | link image | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap | fullscreen preview',
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    contextmenu: 'link image imagetools table configurepermanentpen'
  }

  window.tinymce.murph.modes.light = window.tinymce.murph.modes.light || {
    contextmenu: 'link image imagetools table configurepermanentpen',
    quickbars_selection_toolbar: 'bold italic',
    toolbar: 'undo redo | bold italic underline'
  }
}

const buildConfiguration = (conf) => {
  return Object.assign({}, window.tinymce.murph.configurationBase, conf)
}

const makeId = () => {
  let result = ''
  const characters = 'abcdefghijklmnopqrstuvwxyz0123456789'
  const charactersLength = characters.length

  for (let i = 0; i < 20; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength))
  }

  return 'tinymce-' + result
}

const doInitEditor = () => {
  $(window.tinymce.murph.selector).each((i, v) => {
    const element = $(v)
    let id = null

    if (element.attr('id')) {
      id = element.attr('id')
    } else {
      id = makeId()
      element.attr('id', makeId)
    }

    let mode = element.attr('data-tinymce')

    if (!mode) {
      mode = 'default'
    }

    if (!Object.prototype.hasOwnProperty.call(window.tinymce.murph.modes, mode)) {
      return
    }

    const conf = buildConfiguration(window.tinymce.murph.modes[mode])
    conf.mode = 'exact'
    conf.elements = id

    window.tinymce.init(conf)
  })
}

module.exports = function () {
  if (typeof tinymce === 'undefined') {
    return
  }

  const observer = new MutationObserver(doInitEditor)
  const config = { attributes: false, childList: true, subtree: true }
  observer.observe(document.querySelector('body'), config)

  doInitEditor()
}
