<template>
  <!-- border border-1 border-red-400 -->
  <div class="md:w-9/12 mx-auto flex flex-col bg-white">

    <div v-if="state.loading === false && state.res.data.length > 0" class="flex justify-center md:justify-end mt-4 md:mt-4 py-2">
      <Pagination :paginator="state.res.meta" @changePage="handlePageChange" />
    </div>

    <div v-for="resource in state.res.data" :key="resource.id">

      <div v-if="resource.type === ResourceType.link">
          <Link :link="resource.link" />
      </div>

      <div v-if="resource.type === ResourceType.html_snippet">
        <HtmlSnippet :html-snippet="resource.html_snippet" />
      </div>

      <div v-if="resource.type === ResourceType.file">
        <File :file="resource.file" :resource-id="resource.id"  :resource-title="resource.title"/>
      </div>
    </div>

    <div v-if="state.loading === false && state.res.data.length > 0" class="flex justify-center md:justify-end mt-4 md:mt-4 py-2">
      <Pagination :paginator="state.res.meta" @changePage="handlePageChange" />
    </div>

  </div>
</template>

<script lang="ts">

import {defineComponent, onMounted, reactive} from "vue"
import {PaginatedResponse, ResourceType} from "@/compositions/Resource"
import {PaginationLinks, PaginationMeta} from "@/compositions/Pagination"
import Request, {ErrorBag} from "@/plugins/Request"
import {useRoute} from "vue-router"
import {Page, QueryParams} from "@/compositions/QueryParams"

import HtmlSnippet from "@/components/Resource/HtmlSnippet.vue"
import Link from "@/components/Resource/Link.vue"
import File from "@/components/Resource/File.vue"
import Pagination from "@/components/Pagination.vue"

export default defineComponent({
  components: {
      HtmlSnippet, Link, File, Pagination
  },
  setup() {

    let paginatedRes: PaginatedResponse = {
      data: [],
      links: {} as PaginationLinks,
      meta: {} as PaginationMeta,
    }

    let errorBag: ErrorBag = {
      message: null,
      errors: [],
    }

    let r = useRoute()

    const page : Page = Number(r.query['page']) || 1

    // todo change resource type
    let q: QueryParams = {
      page,
      resource_type: ResourceType.any,
    }

    let state = reactive({
      loading: false,
      res: paginatedRes,
      errorBag,
      q,
    })

    function fetchResources(page: Page) {
      state.q.page = page

      state.loading = true

      const r = new Request()
          r
          .to("resources.index", [])
          .queryParams(state.q)
          .asAdmin()
          .success((res) => {
            state.res = res as PaginatedResponse
          })
          .error((err) => {
            state.errorBag = err as ErrorBag
          })
          .finally(() => (state.loading = false))
          .send()
    }

    function handlePageChange(event : any) {
      fetchResources(event.page)
    }

    onMounted(() => {
      fetchResources(state.q.page ?? 1)
    })

    return {
      state,
      ResourceType, handlePageChange
    }
  }
})

</script>