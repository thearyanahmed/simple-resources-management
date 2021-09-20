<template>
  <button :type="type" :disabled="disabled || processing" :class="classes">
<!--    use slots? -->
    {{ processing ? 'Processing' : text }}
  </button>
</template>

<script lang="ts">
import { defineComponent } from "vue"

export default defineComponent({
  props: {
    type: {
      type: String,
      required: false,
      default: 'button',
    },
    processing: {
      type: Boolean,
      required: false,
      default: false,
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
    text: {
      type: String,
      required: true,
    },
    classes: {
      type: String,
      required: false,
      default: 'py-2 px-4 rounded',
    }
  }
})
</script>