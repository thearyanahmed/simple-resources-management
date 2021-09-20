<template>
  <div>
    <p>{{ file.abs_url }}</p>

    <button @click="download" :disabled="state.processing">{{ downloadFileText  }}</button>
  </div>
</template>

<script lang="ts">

import {computed, defineComponent, PropType, reactive,} from "vue"
import { File } from "@/compositions/Resource"
import Request, {ErrorBag} from "@/plugins/Request";
import { saveFileFromStream } from "@/compositions/Utils"

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
    let errorBag: ErrorBag = {
      message: null,
      errors: [],
    }

    const state = reactive({
      processing: false,
      errorBag
    })

    const downloadFileText = computed(() => {
        return state.processing ? 'Downloading...' : 'Download file'
    })

    function download() {

      state.errorBag.errors = []

      state.processing = true

      const r = new Request()

      r.to('resources.download',[props.resourceId])
      .asRegularUser()
      .success((res) => {
        const saveAs: string = props.resourceTitle + '.pdf'

        saveFileFromStream([res],saveAs)
      })
      .error((err) => state.errorBag = err )
      .finally(() => state.processing = true)
      .download()
    }

    return {
      state, download, downloadFileText
    }
  }
})

</script>