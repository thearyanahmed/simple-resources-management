<template>
  <div v-if="displayMsg">
    <p class="text-black capitalize">{{ message }}</p>
  </div>
</template>

<script lang="ts">
import {defineComponent, reactive, watch} from "vue"

export default defineComponent({
  props: {
    message: {
      required: false,
      type: String,
    },
    timeout: {
      required: false,
      type: Number,
      default: 3000,
    }
  },
  setup(props) {
    const state = reactive({
      displayMsg: true
    })

    watch(()=> props.message, () => {
      setTimeout(() => {
        state.displayMsg = false
      },props.timeout)
    })

    return state
  }
})

</script>