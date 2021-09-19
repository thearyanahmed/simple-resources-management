<template>
  <div class="w-full">
    Welcome to admin page.

    <Loading v-if="state.loading"/>

    <div v-else>
      <NoDataFound v-if="state.res.data.length === 0"/>
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
              <Table :headers="headers">
                <tr v-for="(resource,i) in state.res.data" :key="i">

                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-600">#{{ resource.id }} on <span class="text-xs text-gray-400">{{ displayDate(resource.created_at) }}</span></div>
                    <div class="break-all text-sm text-gray-900">
                      <p class="break-all">{{ resource.title }}</p>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      Active
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="#" class="text-indigo-600 hover:text-indigo-900">
                      Edit
                    </a>
                  </td>
                </tr>
              </Table>
            </div>
            <div class="flex justify-center md:justify-end mt-4 md:mt-4 py-2">
              <Pagination :paginator="state.res.meta" @changePage="handlePageChange" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { useRoute } from 'vue-router'

import {defineComponent, reactive, onMounted} from "vue";
import {PaginatedResponse} from "@/compositions/Resource";
import {PaginationLinks, PaginationMeta} from "@/compositions/Pagination";
import Request, {ErrorBag} from "@/plugins/Request";
import {Page, QueryParams} from "@/compositions/QueryParams";
import { displayDate } from "@/compositions/Utils";

import Loading from '@/components/Loading.vue'
import NoDataFound from '@/components/Loading.vue'
import Table from '@/components/Table.vue'
import Pagination from '@/components/Pagination.vue'

export default defineComponent({
  components: {
    Loading, NoDataFound, Table, Pagination
  },
  setup() {
    let paginatedRes: PaginatedResponse = {
      data: [],
      links: {} as PaginationLinks,
      meta: {} as PaginationMeta,
    };

    let errorBag: ErrorBag = {
      message: null,
      errors: [],
    };

    let r = useRoute()

    const page : Page = r.query['page'] ?? 1
    const per_page : Number = r.query['per_page'] ?? 10

    let q: QueryParams = {
      per_page,
      page
    };

    let state = reactive({
      loading: false,
      res: paginatedRes,
      errorBag,
      page,
      per_page,
      q
    });

    const headers = ['title', 'type', 'action']

    function handlePageChange(event) {
        fetchResources(event.page)
    }

    function fetchResources(page: Page) {
      state.q.page = page

      state.loading = true;

      (new Request())
          .to("resources.index", [])
          .with(state.q)
          .asAdmin()
          .success((res) => {
            state.res = res as PaginatedResponse;
          })
          .error((err) => {
            state.errorBag = err as ErrorBag;
          })
          .finally(() => (state.loading = false))
          .send();
    }

    state.loading = true;

    onMounted(() => {
      fetchResources(state.page);
    });
    return {
      //accessors
      state, headers,

      // funcs
      fetchResources, displayDate, handlePageChange
    };
  },
});
</script>