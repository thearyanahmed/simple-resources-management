<template>
  <div class="w-full">
    <AdminAreaDialogue />

    <div class="flex flex-col">
      <div class="w-full flex justify-between my-4">
        <div class="flex flex-row space-x-2 w-8/12">
          <input id="title" v-model="state.search.title" placeholder="search by title" class="shadow appearance-none border rounded w-6/12 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" max="255"
                 type="text">

          <select id="resource-type" v-model="state.search.resource_type" class="block appearance-none border bg-white px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
            <option :value="ResourceType.any">Any</option>
            <option :value="ResourceType.link">Link</option>
            <option :value="ResourceType.html_snippet">Html Snippet</option>
            <option :value="ResourceType.file">File ( PDF )</option>
          </select>
          <a href="#" @click="handleSearch" class="text-white bg-blue-500 hover:bg-blue-600 rounded hover:text-gray-100 hover:font-medium py-2 px-2 md:mx-2">
            search
          </a>
          <a href="#" @click="clearSearch" class="text-gray-500 hover:font-medium py-2 px-2 md:mx-2">
            clear search
          </a>
        </div>

<!--        div needed for styling-->
        <div class="w-4/12 flex justify-end">
          <router-link :to="{ name: 'admin.resources.create' }"
                       class="text-white bg-blue-500 hover:bg-blue-600 rounded hover:text-gray-100 hover:font-medium py-2 px-2 md:mx-2">
            Create a new resource
          </router-link>
        </div>
      </div>

      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <Table :headers="headers">

              <tr v-if="state.loading">
                <td colspan="3" class="text-center py-8">
                  loading...
                </td>
              </tr>
              <tr v-else-if="state.loading === false && state.res.data.length === 0">
                <td colspan="3" class="text-center py-8">No data found</td>
              </tr>
              <tr v-else v-for="(resource,i) in state.res.data" :key="i" :class="state.deletedIds.indexOf(resource.id) > -1 ? 'bg-gray-200': ''">

                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-400">
                    #{{ resource.id }} @
                    <span class="text-xs">{{ displayDate(resource.created_at) }}</span>
                  </div>

                  <div class="break-all text-sm text-gray-900">
                    <p class="break-all">
                      {{ resource.title }}
                    </p>
                  </div>
                </td>
                <td class="px-6 py-4 text-center whitespace-nowrap">
                  <ResourceTypeBadge :resource-type="resource.type" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div v-if="state.deletedIds.indexOf(resource.id) === -1" class="flex flex-col">
                    <router-link :to="{ name: 'admin.resources.edit', params: { id: resource.id } }" class="text-indigo-600 hover:text-indigo-900">
                      Edit
                    </router-link>
                    <a href="#" @click="handleDeletion(resource.id)"  class="text-red-600 hover:text-red-900">
                      Delete
                    </a>
                  </div>
                  <div v-else>
                    unavailable
                  </div>
                </td>
              </tr>
            </Table>
          </div>
          <div v-if="state.loading === false && state.res.data.length > 0" class="flex justify-center md:justify-end mt-4 md:mt-4 py-2">
            <Pagination :paginator="state.res.meta" @changePage="handlePageChange" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import {useRoute} from 'vue-router'

import {defineComponent, onMounted, reactive} from "vue"
import {PaginatedResponse, ResourceType} from "@/compositions/Resource"
import Request, {ErrorBag} from "@/plugins/Request"
import {Page, QueryParams} from "@/compositions/QueryParams"
import {displayDate, objectToFormData, emptyPaginatedResponse, emptyErrorBag} from "@/compositions/Utils"

import Table from '@/components/Table.vue'
import Pagination from '@/components/Pagination.vue'
import ResourceTypeBadge from '@/components/Resource/Type.vue'
import AdminAreaDialogue from '@/components/AdminAreaDialogue.vue'

export default defineComponent({
  components: {
    Table, Pagination, ResourceTypeBadge, AdminAreaDialogue
  },
  setup() {
    let paginatedRes = emptyPaginatedResponse()
    let errorBag = emptyErrorBag()

    let r = useRoute()

    let q: QueryParams = {
      page: Number(r.query['page']) || 1,
      title: null,
      resource_type: ResourceType.any
    }

    const deletedIds : number[] = []

    let state = reactive({
      loading: false,
      res: paginatedRes,
      errorBag,
      q,
      deletedIds,
      search: {
        resource_type: ResourceType.any,
        title: null,
      }
    })

    // table headers
    const headers = ['title', 'type', '']

    // functions
    function handlePageChange(event : any) {
        fetchResources(event.page)
    }

    function handleSearch() {
      state.q.title = state.search.title
      state.q.resource_type = state.search.resource_type

      state.q.page = 1
      fetchResources(state.q.page)
    }

    function clearSearch() {
      state.search.title = null
      state.search.resource_type = ResourceType.any

      state.q.title = state.search.title
      state.q.resource_type = state.search.resource_type

      state.q.page = 1
      fetchResources(state.q.page)
    }

    function handleDeletion(id: number) {
      if(! confirm('Are you sure?')) {
        return
      }

      const formData = objectToFormData({ _method: 'DELETE' })

      const r = new Request()
        r.to('resources.delete',[id])
        .with(formData)
        .success((res) => {
          state.deletedIds.push(id)
          alert(res.message)
        })
        .error((err) => {
          alert(err.message)
        })
        .asAdmin()
        .send()
    }

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

    onMounted(() => {
      fetchResources(state.q.page ?? 1)
    })
    return {
      state, headers, ResourceType,
      displayDate, handlePageChange, handleDeletion, handleSearch, clearSearch
    }
  },
})
</script>