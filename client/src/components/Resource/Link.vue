<template>
  <div>
    <a :href="link.link"
       :target="link.opens_in_new_tab ? '_blank': '_self'">
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