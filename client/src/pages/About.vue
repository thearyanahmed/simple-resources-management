<template>
  <div class="w-2/6 mx-auto">

    <div v-if="state.loading">
      loading
    </div>
    <div v-else>
      <div v-if="state.errorBag.errors.length > 0">
        <ul>
          <li class="text-red-500" v-for="(err,i) in state.errorBag.errors" :key="i">{{ err }}</li>
        </ul>
      </div>
      <div>

        Count {{ state.resources.length }}
        <div v-if="state.resources.length === 0">
          sorry do data found!
        </div>

        <div v-else>
          <ul>
            <li class="text-green-400" v-for="(res,i) in state.resources" :key="i">
              <strong>{{ res.id }}</strong> {{ res.title }}
            </li>
          </ul>

          <button v-if="state.res.meta.last_page > state.res.meta.current_page" @click="fetchResources( state.res.meta.current_page + 1 )">
            load more
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script lang="ts">

import { defineComponent, onMounted, reactive } from 'vue';

import { Page, QueryParams } from "@/compositions/QueryParams";
import Request, {ErrorBag} from "@/plugins/Request";
import {PaginatedResponse, PaginationLinks, PaginationMeta, Resource} from "@/compositions/Resource";

export default defineComponent({
    setup() {

      let paginatedRes :PaginatedResponse = {
        data: [],
        links: {} as PaginationLinks,
        meta: {} as PaginationMeta,
      }

      let errorBag :ErrorBag = {
        message: null,
        errors: []
      }

      let resources : Resource[] = [] as Resource[]

      let state = reactive({
        loading: false,
        res : paginatedRes,
        resources,
        errorBag
      })

      function fetchResources(page : Page) {
        state.loading = true

        const q :QueryParams = {
          per_page : 10,
          page: page,
        };

        let req = new Request()

        state.loading = true

        req.to('resources.index',[])
          .with(q)
          .success((res) => {
            state.res = res as PaginatedResponse
            state.resources.push(...state.res.data)
          })
            .error(err => {
              state.errorBag = err as ErrorBag
            })
            .finally(() => state.loading = false )
          .send()
      }
      state.loading = true

      onMounted(() => {
        setTimeout(() => {
          fetchResources(1)
        }, 2000)
      })

      console.log('state',state)
      return { state , fetchResources }
    }
})
</script>