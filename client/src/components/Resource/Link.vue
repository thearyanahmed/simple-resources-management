<template>
  <div class="p-4 my-4 border border-dashed border-2 rounded border-yellow-400 bg-white">
    <a :href="link.link"
       :target="link.opens_in_new_tab ? '_blank': '_self'"
       class="p-2 text-gray-500 underline">
      {{ link.link }}
    </a>
  </div>
</template>

<script lang="ts">

import {defineComponent, PropType} from "vue"
import { Link } from "@/compositions/Resource"

export default defineComponent({
  props: {
    link: {
      required: true,
      type: Object as PropType<Link>,
    }
  },
})

</script>