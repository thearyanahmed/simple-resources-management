<template>
  <div>
    <p>{{ file.abs_url }}</p>

    <button @click="download" :disabled="state.processing">Download file</button>
  </div>
</template>

<script lang="ts">

import {defineComponent, PropType, reactive,} from "vue"
import { File } from "@/compositions/Resource"
import Request from "@/plugins/Request";

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

    const state = reactive({
      processing: false
    })

    function download() {

      state.processing = true

      const r = new Request()

      r.to('resources.download',[props.resourceId])
      .asRegularUser()
      // .headers({'Content-Type': 'multipart/form-data' })
      .success((res) => {
        console.log('success',res)
        const blob = new Blob([res], { type: 'application/pdf' })

        const url = window.URL.createObjectURL(blob)
        let link = document.createElement('a');

        //@ts-ignore
        link.style = "display: none";
        link.download = 'filename.pdf'
        link.href = url

        document.body.appendChild(link);
        link.click()


      })
      .error((err) => console.log('err',err))
      .finally(() => state.processing = false)
      .download()

      console.log('download')
    }

    return {
      state, download
    }
  }
})

</script>