<template>
    <div>
        <nav aria-label="breadcrumb bg-light">
            <ol class="breadcrumb mb-0 float-right file-manager-views">
                <li class="breadcrumb-item">
                    <span class="fa fa-grip-horizontal" v-on:click="setView('grid')"></span>
                </li>
                <li class="breadcrumb-item">
                    <span class="fa fa-list" v-on:click="setView('list')"></span>
                </li>
            </ol>

            <ol class="breadcrumb mb-0 float-right file-manager-actions">
                <li class="breadcrumb-item">
                    <span class="fa fa-upload text-primary" v-bind:data-modal="generateUploadLink(directory)"></span>
                    <span class="fa fa-folder-plus text-primary" v-bind:data-modal="generateNewDirectoryLink(directory)"></span>
                </li>
            </ol>

            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item" v-for="item in breadcrumb">
                    <a href="#" v-on:click="setDirectory(item.path)" v-html="item.label"></a>
                </li>
            </ol>
        </nav>

        <div class="card-deck" v-if="view == 'grid'">
            <div v-if="parent" class="card mt-3 ml-3 mb-3 border-0">
                <div class="card-body p-2">
                    <div class="card-text" v-on:dblclick="setDirectory(parent)">
                        <div class="text-center display-4 text-warning">
                            <span class="fa fa-folder"></span>
                        </div>

                        <div class="text-center">
                            ..
                        </div>
                    </div>
                </div>
            </div>

            <div v-for="item in directories" class="card mt-3 ml-3 mb-3 border-0">
                <div class="card-body p-2">
                    <div class="card-text" v-on:dblclick="setDirectory(item.path)" v-bind:data-modal="generateInfoLink(item, true, context)">
                        <div class="text-center">
                            <div class="display-4 text-warning">
                                <span class="fa fa-folder"></span>
                            </div>

                            <div v-if="item.locked" class="file-manager-grid-lock">
                                <span class="btn btn-sm">
                                    <span class="fa fa-lock"></span>
                                </span>
                            </div>
                        </div>

                        <div class="text-center">
                            <span v-html="item.basename"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-for="item in files" class="card mt-3 ml-3 mb-3 border-0" v-on:click="modalUrl = generateInfoLink(item, null, context)" v-bind:data-modal="generateInfoLink(item, null, context)">
                <div class="card-body p-2">
                    <div class="card-text">
                        <div class="text-center">
                            <div class="display-4 text-muted">
                                <FileIcon v-bind:mime="item.mime" v-bind:path="item.webPath" v-bind:thumb="true" />
                            </div>

                            <div v-if="item.locked" class="file-manager-grid-lock">
                                <span class="btn btn-sm">
                                    <span class="fa fa-lock"></span>
                                </span>
                            </div>
                        </div>

                        <div class="text-center">
                            <span v-html="item.basename"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive" v-if="view == 'list'">
            <table class="table">
                <tr v-if="parent" v-on:dblclick="setDirectory(parent)">
                    <td width="10">
                        <span class="fa fa-folder text-warning"></span>
                    </td>
                    <td>
                        ..
                    </td>
                </tr>

                <tr v-for="item in directories" v-on:dblclick="setDirectory(item.path)" v-bind:data-modal="generateInfoLink(item, true, context)">
                    <td width="10">
                        <span class="fa fa-folder text-warning"></span>
                    </td>
                    <td>
                        <div v-if="item.locked" class="float-right">
                            <span class="btn btn-sm btn-light">
                                <span class="fa fa-lock"></span>
                            </span>
                        </div>

                        <span v-html="item.basename"></span>
                    </td>
                </tr>
                <tr v-for="item in files">
                    <td width="10">
                        <FileIcon v-bind:mime="item.mime" v-bind:path="item.webPath" v-bind:thumb="false" />
                    </td>
                    <td v-on:click="modalUrl = generateInfoLink(item, null, context)" v-bind:data-modal="generateInfoLink(item, null, context)">
                        <div v-if="item.locked" class="float-right">
                            <span class="btn btn-sm btn-light">
                                <span class="fa fa-lock"></span>
                            </span>
                        </div>

                        <span v-html="item.basename"></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<style scoped>
.card {
    margin-right: 5px;
    flex: 0 0 170px;
    cursor: pointer;
}

* {
    user-select: none;
}

tr {
    cursor: pointer;
}

.file-manager-views {
    cursor: pointer;
}

.file-manager-grid-lock {
    margin-top: -26px;
    padding-left: 40px;
}

.breadcrumb {
    border-radius: 0;
}

.file-manager-actions .fa {
    padding: 3px;
    cursor: pointer;
}
</style>

<script>
import Routing from '../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js'
import FileIcon from './FileIcon'

const axios = require('axios').default
const $ = require('jquery')
const routes = require('../../../../../public/js/fos_js_routes.json')

Routing.setRoutingData(routes)

export default {
  name: 'Files',
  components: {
    FileIcon
  },
  props: {
    context: {
      type: String,
      required: false
    }
  },
  data () {
    return {
      view: 'list',
      directory: null,
      directories: [],
      breadcrumb: [],
      files: [],
      parent: null,
      modalUrl: null,
      ajax: 0
    }
  },
  methods: {
    setDirectory (directory) {
      if (!directory) {
        directory = '/'
      }
      this.directory = directory
    },
    setView (view) {
      this.view = view

      localStorage.setItem('file-manager.view', view)
    },
    generateInfoLink (item, directory, context) {
      if (directory) {
        return Routing.generate('admin_file_manager_info', {
          file: item.path,
          context: context,
          ajax: this.ajax
        })
      } else {
        return Routing.generate('admin_file_manager_info', {
          file: item.path + '/' + item.basename,
          context: context,
          ajax: this.ajax
        })
      }
    },
    generateUploadLink (directory) {
      return Routing.generate('admin_file_manager_upload', {
        file: directory,
        ajax: this.ajax
      })
    },
    generateNewDirectoryLink (directory) {
      return Routing.generate('admin_file_manager_directory_new', {
        file: directory,
        ajax: this.ajax
      })
    },
    buildBreadcrum (elements) {
      let path = '/'
      this.breadcrumb = []

      for (const i in elements) {
        const element = elements[i]

        if (element !== '/') {
          path = path + '/' + element

          this.breadcrumb.push({
            path: path,
            label: element
          })
        } else {
          this.breadcrumb.push({
            path: '/',
            label: 'Files'
          })
        }
      }
    },
    refresh () {
      const that = this

      axios.get(Routing.generate('admin_file_manager_api_directory', {
        directory: that.directory,
        context: that.context,
        ajax: this.ajax
      }))
        .then((response) => {
          that.buildBreadcrum(response.data.breadcrumb)
          that.parent = response.data.parent
          that.directories = response.data.directories
          that.files = response.data.files

          const query = new URLSearchParams(window.location.search)
          query.set('path', that.directory)

          history.pushState(
            null,
            '',
            window.location.pathname + '?' + query.toString()
          )
        })
        .catch((e) => {
          alert('An error occured')
        })
    }
  },
  mounted () {
    const view = localStorage.getItem('file-manager.view')

    if (['grid', 'list'].indexOf(view) !== -1) {
      this.view = view
    }

    const query = new URLSearchParams(window.location.search)

    if (query.has('path')) {
      this.setDirectory(query.get('path'))
    } else {
      this.setDirectory('/')
    }

    this.ajax = (['crud'].indexOf(this.context) === -1 ? 1 : 0)

    const body = $('body')
    const events = ['file_manager.file.new', 'file_manager.directory.new', 'file_manager.directory.rename']
    const that = this

    $(events).each((k, event) => {
      body.on(event + '.success', () => {
        $('#modal-container').modal('hide')
        that.refresh()
      })
    })

    body.on('file_manager.info.update.success', () => {
      $('*[data-modal="' + that.modalUrl + '"]').click()
    })
  },
  watch: {
    directory (directory) {
      this.refresh()
    }
  }
}
</script>
