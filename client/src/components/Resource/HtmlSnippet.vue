<template>
  <div>
    <p>{{ htmlSnippet.description }}</p>

    <textarea v-model="state.markup" disabled></textarea>
    <p v-if="state.copied">
      Copied to clipboard!
    </p>
    <button @click="copyToClipboard(state.markup)">Copy to clipboard</button>
  </div>
</template>

<script lang="ts">

import {defineComponent, reactive, PropType} from "vue"
import { HtmlSnippet } from "@/compositions/Resource"

export default defineComponent({
  props: {
    htmlSnippet: {
      required: true,
      type: Object as PropType<HtmlSnippet>,
    }
  },

  setup(props) {

    const state = reactive({ copied: false, markup: props.htmlSnippet.markup })

    function copyToClipboard() {
      navigator.clipboard.writeText(props.htmlSnippet.markup)

      setTimeout(() => { state.copied = false }, 1200)
    }

    return {
      state, copyToClipboard
    }
  }
})

</script>