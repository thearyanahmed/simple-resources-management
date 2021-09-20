<template>
  <div class=" p-4 w-full mx-auto rounded-md">

    <p class="capitalize w-full text-md font-bold text-red-500">
      {{ message }}
    </p>
    <ul>
      <li v-for="(err, i) in errors" :key="i" class="text-red-500">{{ err }}</li>
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
    },
    message: {
      type: String,
      required: false,
      default: "",
    }
  }
})
</script>

