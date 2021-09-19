<template>
  <div class="w-full">
    Welcome to admin page.

    <Loading v-if="state.loading"/>

    <div v-else>
      <div class="flex flex-col">
        <div class="w-6/12 mx-auto">
          <form class="bg-gray-100 shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                Title
              </label>
              <input v-model="state.form.title" id="title" type="text" max="255" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div v-if="state.resource.type === resourceType.link">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="link">
                  Link
                </label>
                <input v-model="state.form.link" id="link" type="text" max="255" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              </div>
              <div class="mb-4">
                <label class="md:w-2/3 block text-gray-500 font-bold">
                  <input v-model="state.form.opens_in_new_tab" class="mr-2 leading-tight" type="checkbox">
                  <span class="text-sm">
                    Open link in new tab
                  </span>
                </label>
              </div>
            </div>

            <div v-if="state.resource.type === resourceType.html_snippet">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                  Description
                </label>
                <textarea v-model="state.form.description" id="description" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="markup">
                  Html Markup
                </label>
                <textarea v-model="state.form.markup" id="markup" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
              </div>
            </div>

            <div class="flex items-center justify-between md:justify-end">
              <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { useRoute } from 'vue-router'
import router from "@/router";

import { defineComponent, reactive, onMounted} from "vue";
import Request, {ErrorBag} from "@/plugins/Request";
import { displayDate } from "@/compositions/Utils";

import {Resource, ResourceType as resourceType, resourceToFormFactory, ResourceForm} from "@/compositions/Resource";
import Loading from '@/components/Loading.vue'

export default defineComponent({
  components: {
    Loading,
  },
  setup() {
    // check if route param is number or not
    let r = useRoute()

    if (Number.isNaN(r.params.id)) {
      alert('id must be a number. redirecting')

      router.push('admin.resources.index')
    }

    let id: number = Number(r.params.id)

    let errorBag: ErrorBag = {
      message: null,
      errors: [],
    };

    let resource : Resource = {} as Resource
    let form : ResourceForm = {} as ResourceForm

    let state = reactive({
      id,
      loading: false,
      errorBag,
      resource,
      form,
    });

    function fetchResource(id : number) {
        state.loading = true

        let r = new Request()

          r.to('resources.edit',[id])
          .success((res) => {
              const r : Resource = res as Resource
              state.resource = r
              state.form = resourceToFormFactory(r,r.type)
          })
          .error((err) => {
            console.log('err',err)
          })
          .finally(() => state.loading = false)
          .asAdmin()
          .send()
    }

    onMounted(() => {
      fetchResource(state.id)
    });
    return {
      //accessors
      state, resourceType,

      // funcs
      displayDate,
    };
  },
});
</script>