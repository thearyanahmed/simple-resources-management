<template>
  <div class="w-full">
    Welcome to admin page.

    <Loading v-if="state.loading"/>

    <div v-else>
      <div class="flex flex-col">
        <div class="w-6/12 mx-auto">
          <Errors v-if="state.errorBag.errors.length > 0" :errors="state.errorBag.errors"/>
          <form class="bg-gray-100 shadow-md rounded px-8 pt-6 pb-8 mb-4" method="POST"
                @submit.prevent="updateResource">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                Title
              </label>
              <input id="title" v-model="state.form.title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" max="255" required
                     type="text">
            </div>

            <div v-if="state.resource.type === resourceType.link">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="link">
                  Link
                </label>
                <input id="link" v-model="state.form.link" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" max="255" required
                       type="text">
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
                <label class="block text-gray-700 text-sm font-bold mb-2">
                  Description
                </label>
                <textarea id="description" v-model="state.form.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          required></textarea>
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                  Html Markup
                </label>
                <textarea id="markup" v-model="state.form.markup" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          required></textarea>
              </div>
            </div>
            <div v-if="state.resource.type === resourceType.file">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
                  Upload a new file
                </label>

                <div class="border border-dashed rounded-md border-gray-500 relative">
                  <input id="file" accept="application/pdf" class="cursor-pointer relative block opacity-0 w-full h-full p-20 z-50" required type="file"
                         @change="handleChoseFile($event)">
                  <div class="text-center p-10 absolute top-0 right-0 left-0 m-auto">
                    <h4 v-if="selectedFileName" class="text-xl">
                      You have selected <span class="font-bold">{{ selectedFileName }}</span>
                    </h4>

                    <h4>
                      drop files here to upload
                      <br/>or
                    </h4>
                    <p class="text-gray-900">Select Files</p>
                  </div>
                </div>

                <a :href="state.form.file" class="text-blue-400 underline mt-4" target="_blank">View current file</a>
              </div>

            </div>

            <div class="flex items-center justify-between md:justify-end">
              <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                      type="submit">
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
import {useRoute} from 'vue-router'
import router from "@/router";

import {computed, defineComponent, onMounted, reactive} from "vue";
import Request, {ErrorBag} from "@/plugins/Request";
import {displayDate} from "@/compositions/Utils";

import {Resource, ResourceForm, resourceToFormFactory, ResourceType as resourceType} from "@/compositions/Resource";
import Loading from '@/components/Loading.vue'
import Errors from '@/components/DisplayErrors.vue'
import { objectToFormData } from '@/compositions/Utils'

export default defineComponent({
  components: {
    Loading, Errors
  },
  setup: function () {
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

    let resource: Resource = {} as Resource
    let form: ResourceForm = {
      _method: 'PATCH',
    } as ResourceForm

    let selectedFile: any = null

    let state = reactive({
      id,
      loading: false,
      errorBag,
      resource,
      form,
      selectedFile,
    });

    const selectedFileName = computed(() => {

      if (state.selectedFile !== null) {
        return state.selectedFile ? state.selectedFile.name : null
      }

      return null
    })

    function fetchResource(id: number) {
      state.loading = true
      state.errorBag.errors = []

      let r = new Request()

      r.to('resources.edit', [id])
          .success((res) => {
            const r: Resource = res as Resource
            state.resource = r
            state.form = resourceToFormFactory(r, r.type)
          })
          .error((err) => {
            state.errorBag = err
          })
          .finally(() => state.loading = false)
          .asAdmin()
          .send()
    }

    function handleChoseFile(event: any) {
      state.selectedFile = event.target.files[0]
    }

    function updateResource() {
      state.form._method = 'PUT'

      let request = (new Request()).to('resources.update', [state.id]).asAdmin()

      if (state.selectedFile) {
        state.form.file = state.selectedFile

        request.headers({
          'Content-Type' : 'multipart/form-data'
        })
      }

      const formData = objectToFormData(state.form)

      request.with(formData)
      request.success(res => console.log('res',res))
      request.success(err => console.error('err',err))

      request.send()

      console.log('state,form', state.form)
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