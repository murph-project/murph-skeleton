<template>
    <div>
        <nav aria-label="breadcrumb" class="d-flex justify-content-between">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item" v-for="item in breadcrumb">
                    <a class="btn btn-sm" href="#" v-on:click="setDirectory(item.path)" v-html="item.label"></a>
                </li>
                <li v-if="isLoading" class="ml-3">
                    <div class="spinner-border spinner-border-sm" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </li>
            </ol>

            <div class="d-flex">
                <div class="breadcrumb mb-0 file-manager-actions">
                    <span class="btn btn-sm btn-primary ml-1" v-bind:data-modal="generateUploadLink(directory)">
                        <span class="fa fa-upload" v-bind:data-modal="generateUploadLink(directory)"></span>
                    </span>
                    <span class="btn btn-sm btn-primary ml-1" v-bind:data-modal="generateNewDirectoryLink(directory)">
                        <span class="fa fa-folder-plus" v-bind:data-modal="generateNewDirectoryLink(directory)"></span>
                    </span>
                </div>

                <div class="breadcrumb mb-0 file-manager-views">
                    <select v-model="sort" class="form-control form-control-sm d-inline w-auto ml-1">
                        <option value="name">Name</option>
                        <option value="modification_date">Date</option>
                    </select>
                    <select v-model="sortDirection" class="form-control form-control-sm d-inline w-auto ml-1">
                        <option value="asc">ASC</option>
                        <option value="desc">DESC</option>
                    </select>
                    <span class="btn btn-sm btn-dark ml-1" v-on:click="setView('grid')">
                        <span class="fa fa-grip-horizontal" v-on:click="setView('grid')"></span>
                    </span>
                    <span class="btn btn-sm btn-dark ml-1" v-on:click="setView('list')">
                        <span class="fa fa-list" v-on:click="setView('list')"></span>
                    </span>
                </div>
            </div>
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

.breadcrumb, nav {
    border-radius: 0;
    background: #e9ecef;
}

.file-manager-actions .fa {
    padding: 3px;
    cursor: pointer;
}

.breadcrumb-item + .breadcrumb-item::before {
    margin-top: 4px;
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
      sort: 'name',
      sortDirection: 'asc',
      parent: null,
      modalUrl: null,
      ajax: 0,
      isLoading: false
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
    saveSort () {
      localStorage.setItem('file-manager.sort', this.sort)
      localStorage.setItem('file-manager.sortDirection', this.sortDirection)
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
      this.isLoading = true
      this.files = []
      this.directories = []

      axios.get(Routing.generate('admin_file_manager_api_directory', {
        directory: that.directory,
        context: that.context,
        ajax: this.ajax,
        _sort: this.sort,
        _sort_direction: this.sortDirection,
        time: Date.now(),
      }))
        .then((response) => {
          that.buildBreadcrum(response.data.breadcrumb)
          that.parent = response.data.parent
          that.directories = response.data.directories
          that.files = response.data.files
          that.isLoading = false

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
    const sort = localStorage.getItem('file-manager.sort')
    const sortDirection = localStorage.getItem('file-manager.sortDirection')

    if (['grid', 'list'].indexOf(view) !== -1) {
      this.view = view
    }

    if (['name', 'modification_date'].indexOf(sort) !== -1) {
      this.sort = sort
    }

    if (['asc', 'desc'].indexOf(sortDirection) !== -1) {
      this.sortDirection = sortDirection
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
    },
    sort (sort) {
      this.saveSort()
      this.refresh()
    },
    sortDirection (sortDirection) {
      this.saveSort()
      this.refresh()
    }
  }
}
</script>
