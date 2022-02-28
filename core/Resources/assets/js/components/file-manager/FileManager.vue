<template>
    <div>
        <div class="row">
            <div class="col">
                <Files v-bind:context="context" />
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>

<script>
import Files from './Files'

export default {
  name: 'FileManager',
  props: {
    context: {
      type: String,
      required: true,
      default: 'crud'
    }
  },
  components: {
    Files
  }
}
</script>
