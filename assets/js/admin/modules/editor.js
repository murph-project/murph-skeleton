const $ = require('jquery')

if (typeof tinymce !== 'undefined') {
  tinymce.murph = tinymce.murph || {}
  tinymce.murph.selector = tinymce.murph.selector || '*[data-tinymce]'
  tinymce.murph.configurationBase = tinymce.murph.configurationBase || {
    base_url: '/vendor/tinymce/',
    cache_suffix: '?v=4.1.6',
    importcss_append: true,
    image_caption: true,
    noneditable_noneditable_class: 'mceNonEditable',
    toolbar_drawer: 'sliding',
    spellchecker_dialog: true,
    tinycomments_mode: 'embedded',
    convert_urls: false,
    init_instance_callback: function (editor) {
      editor.on('SetContent', () => {
        tinymce.triggerSave(false, true)
      })

      editor.on('Change', () => {
        tinymce.triggerSave(false, true)
      })
    }
  }

  tinymce.murph.modes = tinymce.murph.modes || {}

  tinymce.murph.modes.default = tinymce.murph.modes.default || {
    plugins: 'print preview importcss searchreplace visualblocks visualchars fullscreen template table charmap hr pagebreak nonbreaking toc insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars link image code autoresize',
    menubar: 'file edit view insert format tools table tc help',
    toolbar: 'undo redo | bold italic underline strikethrough | link image | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap | fullscreen  preview',
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    contextmenu: 'link image imagetools table configurepermanentpen'
  }

  tinymce.murph.modes.light = tinymce.murph.modes.light || {
    contextmenu: 'link image imagetools table configurepermanentpen',
    quickbars_selection_toolbar: 'bold italic',
    toolbar: 'undo redo | bold italic underline'
  }
}

const buildConfiguration = (conf) => {
  return Object.assign({}, tinymce.murph.configurationBase, conf)
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
  $(tinymce.murph.selector).each((i, v) => {
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

    if (!tinymce.murph.modes.hasOwnProperty(mode)) {
      return
    }

    const conf = buildConfiguration(tinymce.murph.modes[mode])
    conf.mode = 'exact'
    conf.elements = id

    tinymce.init(conf)
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
