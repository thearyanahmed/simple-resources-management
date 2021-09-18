<template>
  <div class="w-2/6 mx-auto">

    <div v-if="state.loading">
      loading
    </div>
    <div v-else>
      <div v-if="state.resources.length > 0">
        <ul>
          <li v-for="(resource,i) in state.resources" :key="i">
              {{ resource.title }}
          </li>
        </ul>
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
import { onMounted, reactive } from 'vue'

export default {
  setup() {

    const state = reactive({
      resources: [],
      loading: false
    })

    onMounted(() => {
      state.loading = true

      getAllResources({ per_page: 10 })
          .success((res) => {
            state.resources = res.data
          })
          .error((err) => {
            console.log('errors',err)
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