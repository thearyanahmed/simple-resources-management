<template>
  <div class="bg-red-200 p-4 w-full mx-auto">
    Sorry, there was a/an error/s.

    <ul>
      <li v-for="(err, i) in errors" :key="i">{{ err }}</li>
    </ul>
  </div>
</template>

<script lang="ts">

import {defineComponent} from 'vue'

export default defineComponent({
  props: {
    errors: {
      type: Array,
      required: true,
    }
  }
})
</script>

