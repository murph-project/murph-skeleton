const Vue = require('vue').default

const FileManager = require('../components/file-manager/FileManager').default

module.exports = () => {
  if (!document.getElementById('file-manager')) {
    return
  }

  return new Vue({
    el: '#file-manager',
    template: '<FileManager context="crud" />',
    components: {
      FileManager
    }
  })
}
