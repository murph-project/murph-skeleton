<template>
    <div>
        <nav aria-label="breadcrumb bg-light">
            <div class="float-right">
            </div>

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
                    <div class="card-text" v-on:dblclick="setDirectory(item.path)" v-bind:data-modal="generateInfoLink(item, true)">
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
            <div v-for="item in files" class="card mt-3 ml-3 mb-3 border-0" v-bind:data-modal="generateInfoLink(item)">
                <div class="card-body p-2">
                    <div class="card-text">
                        <div class="text-center">
                            <div class="display-4 text-muted">
                                <FileIcon v-bind:mime="item.mime" />
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

                <tr v-for="item in directories" v-on:dblclick="setDirectory(item.path)" v-bind:data-modal="generateInfoLink(item, true)">
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
                        <FileIcon v-bind:mime="item.mime" />
                    </td>
                    <td v-bind:data-modal="generateInfoLink(item)">
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
const routes = require('../../../../../public/js/fos_js_routes.json')

export default {
  name: 'Files',
  components: {
    FileIcon
  },
  data () {
    return {
      view: 'list',
      directory: null,
      directories: [],
      breadcrumb: [],
      files: [],
      parent: null
    }
  },
  methods: {
    setDirectory (directory) {
      this.directory = directory
    },
    setView (view) {
      this.view = view

      localStorage.setItem('file-manager.view', view)
    },
    generateInfoLink (item, directory) {
      if (directory) {
        return Routing.generate('admin_file_manager_info', {
          file: item.path
        })
      } else {
        return Routing.generate('admin_file_manager_info', {
          file: item.path + '/' + item.basename
        })
      }
    },
    generateUploadLink (directory) {
      return Routing.generate('admin_file_manager_upload', {
        file: directory
      })
    },
    generateNewDirectoryLink (directory) {
      return Routing.generate('admin_file_manager_directory_new', {
        file: directory
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
    }
  },
  mounted () {
    Routing.setRoutingData(routes)
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
  },
  watch: {
    directory (directory) {
      axios.get(Routing.generate('admin_file_manager_api_directory', {
        directory: this.directory
      }))
        .then((response) => {
          this.buildBreadcrum(response.data.breadcrumb)
          this.parent = response.data.parent
          this.directories = response.data.directories
          this.files = response.data.files

          const query = new URLSearchParams(window.location.search)
          query.set('path', directory)

          history.pushState(
            null,
            '',
            window.location.pathname + '?' + query.toString()
          )
        })
        .catch(() => {
          alert('An error occured')
        })
    }
  }
}
</script>
