<template>
   <span :class="color" class="px-2 text-left inline-flex text-xs leading-5 font-semibold rounded-full">
     {{ text  }}
   </span>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { ResourceType, ResourceTypeText, ResourceTypeColors } from '@/compositions/Resource'

export default defineComponent({
  props:{
    resourceType: {
      type: String,
      required: true,
    }
  },

  setup(props) {
    const type = props.resourceType as ResourceType

    const text = ResourceTypeText[type]

    const color = ResourceTypeColors[type]
    return {
      color, text
    }
  }
})
</script>