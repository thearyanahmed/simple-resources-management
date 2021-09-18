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
        <div v-if="state.api_errors.length > 0">
          <li v-for="(msg,i) in state.api_errors" :key="i">
            {{ msg }}
          </li>
        </div>
        <div v-else>
          Sorry, no resources to show!
        </div>
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
      loading: false,
      api_errors: [],
    })

    onMounted(() => {
      state.loading = true

        state.api_errors = []

        getAllResources({ per_page: 10 })
            .success((res) => {
              state.resources = res.data
            })
            .error((errBag) => {
              state.api_errors = errBag['errors']
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