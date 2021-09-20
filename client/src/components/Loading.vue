<template>
  <div class="text-center py-8">
    {{ message }}
  </div>
</template>

<script>
import { defineComponent } from "vue"

export default defineComponent({
  props: {
    message: {
      required: false,
      default: 'Loading...',
      type: String,
    }
  }
})
</script>