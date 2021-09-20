<template>
  <div class="p-4 my-4 border border-dotted border-2 border-yellow-400 rounded flex flex-row items-center justify-between bg-white bg-opacity-70">
    <p class="text-sm text-gray-500 underline">{{ file.abs_url }}</p>

    <button @click="download" :disabled="state.processing" class="bg-yellow-200 font-bold rounded px-4 py-2">{{ downloadFileText  }}</button>
  </div>
</template>

<script lang="ts">

import {computed, defineComponent, PropType, reactive,} from "vue"
import { File } from "@/compositions/Resource"
import Request from "@/plugins/Request"
import {emptyErrorBag, saveFileFromStream} from "@/compositions/Utils"

export default defineComponent({
  props: {
    file: {
      required: true,
      type: Object as PropType<File>,
    },
    resourceId: {
      required: true,
      type: Number,
    },
    resourceTitle: {
      required: true,
      type: String,
    }
  },

  setup(props) {
    let errorBag = emptyErrorBag()

    const state = reactive({
      processing: false,
      errorBag
    })

    // computed
    const downloadFileText = computed(() => {
        return state.processing ? 'Downloading...' : 'Download file'
    })

    function download() {
      state.errorBag.errors = []
      state.errorBag.message = null

      state.processing = true

      const r = new Request()

      r.to('resources.download',[props.resourceId])
      .asRegularUser()
      .success((res) => {
        const saveAs: string = props.resourceTitle + '.pdf'

        saveFileFromStream([res],saveAs)
      })
      .error((err) => state.errorBag = err )
      .finally(() => state.processing = false)
      .download()
    }

    return {
      state, download, downloadFileText
    }
  }
})

</script>