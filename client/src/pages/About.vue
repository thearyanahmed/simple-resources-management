<template>
  <div class="w-2/6 mx-auto">

    <div v-if="state.loading">
      loading
    </div>
    <div v-else>
      <div v-if="state.resources.length > 0">

      </div>
      <div v-else>
        Sorry, no resources to show!
      </div>
    </div>
  </div>
</template>

<script>
import { getAllResources } from "@/compositions/Resource";
// reactive
import { onMounted } from 'vue'

export default {
  setup() {

    const state = {
      resources: [],
      loading: false
    }

    onMounted(() => {
      state.loading = true

      getAllResources({ per_page: 10 })
          .success((res) => {
            state.resources = res.data
          })
          .endsWith(() => {
            state.loading = false
          })
          .send()
    })

    return {
      state
    }
  },
}
</script>