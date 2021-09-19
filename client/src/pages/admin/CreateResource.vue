<template>
  <div class="w-full">

    <AdminAreaDialogue />
    <div>
      <div class="flex flex-col">
        <div class="w-6/12 mx-auto">

          <form class="bg-gray-100 shadow-md rounded px-8 pt-6 pb-8 mb-4" method="POST"
                @submit.prevent="createResource">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                Title
              </label>
              <input id="title" v-model="state.form.title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" max="255" required
                     type="text">
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="resource-type">
                Resource type
              </label>
              <select id="resource-type" v-model="state.form.resource_type" class="block appearance-none bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                <option value="link">Link</option>
                <option value="html_snippet">Html Snippet</option>
                <option value="file">File ( pdf )</option>
              </select>
            </div>

            <div v-if="state.form.resource_type === resourceType.link">
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

            <div v-if="state.form.resource_type === resourceType.html_snippet">
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
            <div v-if="state.form.resource_type === resourceType.file">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
                  Upload a new file
                </label>

                <div class="border border-dashed rounded-md border-gray-500 relative">
                  <input id="file" required accept="application/pdf" class="cursor-pointer relative block opacity-0 w-full h-full p-20 z-50" type="file"
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
              <Errors v-if="state.errorBag.errors.length > 0" :errors="state.errorBag.errors" :message="state.errorBag.message" class="mb-4" />

              <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                      type="submit">
                Create
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">

import {computed, defineComponent, reactive} from "vue";
import Request, {ErrorBag} from "@/plugins/Request";
import {displayDate, objectToFormData} from "@/compositions/Utils";
// import router from "@/router";

import {
  ResourceForm,
  ResourceType,
  ResourceType as resourceType
} from "@/compositions/Resource";
import Errors from '@/components/DisplayErrors.vue'
import AdminAreaDialogue from '@/components/AdminAreaDialogue.vue'

export default defineComponent({
  components: {
    Errors,AdminAreaDialogue
  },
  setup: function () {

    let errorBag: ErrorBag = {
      message: null,
      errors: [],
    };

    let form: ResourceForm = {} as ResourceForm
    form._method = 'POST'
    form.resource_type = ResourceType.link

    let selectedFile: any = null

    let state = reactive({
      loading: false,
      errorBag,
      form,
      selectedFile,
    });

    const selectedFileName = computed(() => {

      if (state.selectedFile !== null) {
        return state.selectedFile ? state.selectedFile.name : null
      }

      return null
    })

    function handleChoseFile(event: any) {
      state.selectedFile = event.target.files[0]
    }

    function createResource() {

      let request = (new Request()).to('resources.store', []).asAdmin()

      if (state.form.resource_type === ResourceType.file && state.selectedFile) {
        state.form.file = state.selectedFile

        request.headers({
          'Content-Type' : 'multipart/form-data'
        })
      } else {
        delete state.form.file
      }

      if(state.form.resource_type === ResourceType.link) {
        state.form.opens_in_new_tab = state.form.opens_in_new_tab ? 1 : 0
      }

      const formData = objectToFormData(state.form)

      request.with(formData)
      request.success(res => {
        alert(res.message)
        state.selectedFile = null
        // router.push({ name: 'admin.resources.index' })
      })
      request.error((err) => {
        state.errorBag = err
        console.log('state',state.errorBag,err)
      })

      request.send()
    }

    return {
      //accessors
      state, resourceType, selectedFileName,

      // funcs
      displayDate, handleChoseFile, createResource
    };
  },
});
</script>