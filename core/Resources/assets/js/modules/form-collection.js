const $ = require('jquery')

const DeleteHandler = (e) => {
  e.stopPropagation()
  const target = e.target
  let button = $(target)

  if (button.is('[data-collection-delete-container]')) {
    button = button.find('*[data-collection-delete]').first()
  }

  const id = button.attr('data-collection-delete')
  const collection = button.parents('[data-collection]')
  const item = collection.find('*[data-collection-item="' + id + '"]')

  if (confirm('Validez-vous la suppression ?')) {
    item.remove()
    collection.trigger('collection.update')
  }
}

const CollectionInitilizedAndUpdated = (e) => {
  const target = $(e.target)

  target.find('*[data-collection-empty]').toggleClass(
    'd-none',
    target.find('*[data-collection-item]').length !== 0
  )

  target.find('*[data-collection-nonempty]').toggleClass(
    'd-none',
    target.find('*[data-collection-item]').length === 0
  )
}

const FormCollection = () => {
  $('*[data-collection]').on(
    'collection.update',
    CollectionInitilizedAndUpdated
  )

  $('*[data-collection]').on(
    'collection.init',
    CollectionInitilizedAndUpdated
  )

  $('body').on(
    'click',
    '*[data-collection-delete], *[data-collection-delete-container]',
    DeleteHandler
  )

  $('body').on('click', '*[data-collection-add]', (e) => {
    e.stopPropagation()

    const collectionId = $(e.target).attr('data-collection-add')
    const collectionContainer = $('*[data-collection="' + collectionId + '"]')
    const prototypeContent = $('#' + collectionId).html()
    let name = 0

    collectionContainer.find('*[data-collection-item]').each(function () {
      const n = parseInt($(this).attr('data-collection-item'))

      if (n >= name) {
        name = n + 1
      }
    })

    collectionContainer.append(prototypeContent)

    const item = collectionContainer.children('*[data-collection-item]:last-child')
    const deleteBtn = $('<span data-collection-delete="__name__" class="fa fa-trash"></span>')

    item.find('*[data-collection-delete-container]').first().append(deleteBtn)
    item.html(item.html().replace(/__name__/g, name))
    item.attr('data-collection-item', name)

    collectionContainer.trigger('collection.update')
  })

  $('*[data-collection]').trigger('collection.init')
}

module.exports = FormCollection
