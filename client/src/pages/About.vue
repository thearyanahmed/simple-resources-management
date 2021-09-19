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

<script lang="ts">

import { defineComponent, onMounted } from 'vue';

// import { getAllResources } from "@/compositions/Resource";
// reactive
// import { onMounted, reactive } from 'vue'

export default defineComponent({
    setup() {
      const state = {
        resources: [],
        loading: false,
        api_errors: [],
      }

      onMounted(() => {
        state.api_errors = []

      })

      return { state  }
    }
})
</script>