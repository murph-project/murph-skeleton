<template>
    <span>
        <span v-if="!thumb || !thumbnail" v-bind:class="icon"></span>
        <img v-if="thumb && thumbnail" v-bind:src="thumbnail">
    </span>
</template>

<style scoped>
</style>

<script>
import Routing from '../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js'
const routes = require('../../../../../public/js/fos_js_routes.json')

Routing.setRoutingData(routes)

const map = {
  'fa fa-file-pdf': ['application/pdf'],
  'fa fa-file-image': ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
  'fa fa-file-audio': ['application/ogg', 'audio/mp3', 'audio/mpeg', 'audio/wav'],
  'fa fa-file-archive': ['application/zip', 'multipart/x-zip', 'application/rar', 'application/x-rar-compressed', 'application/x-zip-compressed', 'application/tar', 'application/x-tar'],
  'fa fa-file-alt': ['application/rtf'],
  'fa fa-file-excel': ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
  'fa fa-file-powerpoint': ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
  'fa fa-file-video': ['video/x-msvideo', 'video/mpeg']
}

export default {
  name: 'FileIcon',
  data () {
    return {
      icon: null,
      thumbnail: null
    }
  },
  methods: {
    defineIcon () {
      for (const icon in map) {
        if (map[icon].indexOf(this.mime) !== -1) {
          this.icon = icon
          return
        }
      }

      this.icon = 'fa fa-file'
    },
    defineThumbnail () {
      if (['image/png', 'image/jpg', 'image/jpeg', 'image/gif'].indexOf(this.mime) === -1) {
        return
      }

      this.thumbnail = Routing.generate('liip_imagine_filter', {
        filter: 'file_manager_thumbnail_filter',
        path: this.path
      })
    }
  },
  props: {
    mime: {
      type: String,
      required: true
    },
    path: {
      type: String,
      required: true
    },
    thumb: {
      type: Boolean,
      required: true
    }
  },
  mounted () {
    this.defineIcon()
    this.defineThumbnail()
  }
}
</script>
