<template>
  <div class="w-full">
    <AdminAreaDialogue />

    <div class="flex flex-col">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <Table :headers="headers">

              <tr v-if="state.loading">
                <td colspan="3" class="text-center py-8">loading...</td>
              </tr>
              <tr v-else-if="state.loading === false && state.res.data.length === 0">
                <td colspan="3" class="text-center py-8">No data found</td>
              </tr>
              <tr v-else v-for="(resource,i) in state.res.data" :key="i" :class="state.deletedIds.indexOf(resource.id) > -1 ? 'bg-red-400': ''">

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
import { useRoute } from 'vue-router'

import {defineComponent, reactive, onMounted} from "vue"
import {PaginatedResponse} from "@/compositions/Resource"
import {PaginationLinks, PaginationMeta} from "@/compositions/Pagination"
import Request, {ErrorBag} from "@/plugins/Request"
import {Page, QueryParams} from "@/compositions/QueryParams"
import { displayDate, objectToFormData } from "@/compositions/Utils"

import Table from '@/components/Table.vue'
import Pagination from '@/components/Pagination.vue'
import ResourceTypeBadge from '@/components/Resource/Type.vue'
import AdminAreaDialogue from '@/components/AdminAreaDialogue.vue'

export default defineComponent({
  components: {
    Table, Pagination, ResourceTypeBadge, AdminAreaDialogue
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
    const per_page : number = Number(r.query['per_page']) || 10

    let q: QueryParams = {
      per_page,
      page
    }

    const deletedIds : number[] = []

    let state = reactive({
      loading: false,
      res: paginatedRes,
      errorBag,
      page,
      per_page,
      q,
      deletedIds,
    })

    const headers = ['title', 'type', '']

    function handlePageChange(event : any) {
      console.log('event',event)
        fetchResources(event.page)
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
      fetchResources(state.page)
    })
    return {
      state, headers,
      displayDate, handlePageChange, handleDeletion
    }
  },
})
</script>