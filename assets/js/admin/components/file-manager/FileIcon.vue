<template>
    <span v-bind:class="getIcon(mime)"></span>
</template>

<script>
const map = {
  'fa-file-pdf': ['application/pdf'],
  'fa-file-image': ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
  'fa-file-audio': ['application/ogg', 'audio/mp3', 'audio/mpeg', 'audio/wav'],
  'fa-file-archive': ['application/zip', 'multipart/x-zip', 'application/rar', 'application/x-rar-compressed', 'application/x-zip-compressed', 'application/tar', 'application/x-tar'],
  'fa-file-alt': ['application/rtf'],
  'fa-file-excel': ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
  'fa-file-powerpoint': ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
  'fa-file-video': ['video/x-msvideo', 'video/mpeg']
}

export default {
  name: 'FileIcon',
  methods: {
    getIcon (mime) {
      const icons = ['fa']
      let iconFound = false

      for (const icon in map) {
        if (map[icon].indexOf(mime) !== -1) {
          iconFound = true
          icons.push(icon)
        }
      }

      if (!iconFound) {
        icons.push('fa-file')
      }

      return icons
    }
  },
  props: {
    mime: {
      type: String,
      required: true
    }
  }
}
</script>
