<template>
  <div class="p-4 my-4 border border-dotted border-2 rounded flex flex-col border-yellow-400">
    <p class="text-sm font-normal">{{ htmlSnippet.description }}</p>

    <textarea v-model="state.markup" rows="5" disabled class="my-2 bg-gray-50 border border-1 border-solid rounded p-4"></textarea>

    <div class="flex flex-row justify-end space-x-4 my-4">
      <div v-if="state.copied" class="text-gray-400">
        Copied to clipboard!
      </div>

      <button @click="copyToClipboard()" type="button" class="text-gray-700 font-bold">Copy to clipboard</button>
    </div>
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
      state.copied = true 

      navigator.clipboard.writeText(props.htmlSnippet.markup)

      setTimeout(() => { state.copied = false }, 2000)
    }

    return {
      state, copyToClipboard
    }
  }
})

</script>