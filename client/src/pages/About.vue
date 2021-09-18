<template>
  <div class="w-2/6 mx-auto">
    about page
  </div>
</template>

<script>
import {getAllResources} from "@/compositions/Resource";
// reactive
import { onMounted } from 'vue'

export default {
  setup() {

    const state = {
      resources: []
    }

    onMounted(() => {
      console.log('hello -> ')

      getAllResources({ per_page: 10 })
    })

    return {
      state
    }
  },
}
</script>