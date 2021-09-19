<template>
  <div class="w-full">
    Welcome to admin page.

    <Loading v-if="state.loading"/>

    <div v-else>
      <div class="flex flex-col">
        <div class="w-6/12 mx-auto">
          <form @submit.prevent="updateResource" method="POST" class="bg-gray-100 shadow-md rounded px-8 pt-6 pb-8 mb-4">
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
            <div v-if="state.resource.type === resourceType.file">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
                  Upload a new file
                </label>

                <div class="border border-dashed rounded-md border-gray-500 relative">
                  <input type="file" @change="handleChoseFile($event)" accept="application/pdf" required id="file" class="cursor-pointer relative block opacity-0 w-full h-full p-20 z-50">
                  <div class="text-center p-10 absolute top-0 right-0 left-0 m-auto">
                    <h4 class="text-xl"  v-if="selectedFileName">
                      You have selected <span class="font-bold">{{ selectedFileName }}</span>
                    </h4>

                    <h4>
                      drop files here to upload
                      <br/>or
                    </h4>
                    <p class="text-gray-900">Select Files</p>
                  </div>
                </div>

                <a :href="state.form.file" target="_blank" class="text-blue-400 underline mt-4">View current file</a>
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

import { defineComponent, reactive, onMounted, computed } from "vue";
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

    let selectedFile : any = null

    let state = reactive({
      id,
      loading: false,
      errorBag,
      resource,
      form,
      selectedFile,
    });

    const selectedFileName = computed(() => {

      console.log('selected file',state.selectedFile)

      if(state.selectedFile !== null) {
        return state.selectedFile ? state.selectedFile.name : null
      }

      return null
    })

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

    function handleChoseFile(event : any) {
      console.log(event.target.files[0])
      state.selectedFile = event.target.files[0]
    }

    function updateResource() {
      console.log('updating resource')
    }

    onMounted(() => {
      fetchResource(state.id)
    });
    return {
      //accessors
      state, resourceType, selectedFileName,

      // funcs
      displayDate, handleChoseFile, updateResource
    };
  },
});
</script>