<template>
    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
      <button @click="changePage(current_page - 1)"
              :disabled="current_page - 1 === 0"
              :class="current_page - 1 === 0 ? 'text-gray-400' : 'text-gray-900'"
              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium">
        Previous
      </button>
      <button @click="changePage(current_page + 1)"
              :disabled="current_page + 1 >= last_page"
              :class="current_page + 1 >= last_page ? 'text-gray-400' : 'text-gray-900'"
              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium">
        Next page
      </button>
    </nav>
</template>

<script lang="ts">
import { defineComponent, PropType } from "vue"
import { Page } from "@/compositions/QueryParams"
import {PaginationMeta} from "@/compositions/Pagination"

export default defineComponent({
  props: {
      paginator: {
        type: Object as PropType<PaginationMeta>,
        required: true,
      }
  },
  setup(props) {

    function changePage(page: Page) {
      //@ts-ignore
      this.$emit('changePage', {page})
    }

    return  {
      current_page : props.paginator.current_page,
      last_page : props.paginator.last_page,
      changePage,
    }
  }
})

</script>