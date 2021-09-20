<!--<template>-->
<!--  <div class="md:max-w-6xl mx-auto flex flex-col border border-1 bg-white">-->

<!--    <div>-->

<!--    </div>-->
<!--  </div>-->
<!--</template>-->

<!--<script lang="ts">-->

<!--import {defineComponent, reactive} from "vue";-->
<!--import {PaginatedResponse} from "@/compositions/Resource";-->
<!--import {PaginationLinks, PaginationMeta} from "@/compositions/Pagination";-->
<!--import Request, {ErrorBag} from "@/plugins/Request";-->
<!--import {useRoute} from "vue-router";-->
<!--import {Page, QueryParams} from "@/compositions/QueryParams";-->

<!--export default defineComponent({-->
<!--  setup() {-->

<!--    let paginatedRes: PaginatedResponse = {-->
<!--      data: [],-->
<!--      links: {} as PaginationLinks,-->
<!--      meta: {} as PaginationMeta,-->
<!--    }-->

<!--    let errorBag: ErrorBag = {-->
<!--      message: null,-->
<!--      errors: [],-->
<!--    }-->

<!--    let r = useRoute()-->

<!--    const page : Page = Number(r.query['page']) || 1-->

<!--    let q: QueryParams = {-->
<!--      page,-->
<!--      resource_type: 'any'-->
<!--    }-->

<!--    let state = reactive({-->
<!--      loading: false,-->
<!--      res: paginatedRes,-->
<!--      errorBag,-->
<!--      page,-->
<!--      q,-->
<!--    })-->


<!--    function fetchResources(page: Page) {-->
<!--      state.q.page = page-->

<!--      state.loading = true-->

<!--      const r = new Request()-->
<!--      r-->
<!--          .to("resources.index", [])-->
<!--          .queryParams(state.q)-->
<!--          .asAdmin()-->
<!--          .success((res) => {-->
<!--            state.res = res as PaginatedResponse-->
<!--          })-->
<!--          .error((err) => {-->
<!--            state.errorBag = err as ErrorBag-->
<!--          })-->
<!--          .finally(() => (state.loading = false))-->
<!--          .send()-->
<!--    }-->

<!--  }-->
<!--})-->

<!--</script>-->