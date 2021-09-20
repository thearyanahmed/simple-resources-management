<template>
  <div class="w-full">
    <AdminAreaDialogue />

    <div>
      <div class="flex flex-col">
        <div class="w-6/12 mx-auto bg-gray-100 shadow-md rounded px-8 pt-6 pb-8 mb-4">
          <form method="POST" @submit.prevent="updateResource">
            <div class="mb-4 text-center">
                <p class="text-xl">Resources / Edit</p>
            </div>

            <Loading v-if="state.loading" message="loading resources..." />

            <div v-if="state.loading === false && state.resourceNotFound">
                <p class="text-red-500">{{ state.errorBag.message }}</p>
                <p>
                  <router-link :to="{ name: 'admin.resources.index' }" class="text-blue-500 underline hover:font-medium">
                    Back to resources
                  </router-link>
                </p>

            </div>

            <div v-else>
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
                    <input id="file" accept="application/pdf" class="cursor-pointer relative block opacity-0 w-full h-full p-20 z-50" type="file"
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

                  <a href="#" @click="downloadCurrentFile" class="text-blue-400 underline mt-4">View current file</a>
                </div>
              </div>

              <div class="flex flex-col md:flex-row items-center justify-center md:justify-between">
                <div class="flex justify-start">
                  <Errors v-if="state.errorBag.errors.length > 0" :errors="state.errorBag.errors" :message="state.errorBag.message" class="mb-4" />
                  <FlashMessage :message="state.flashMessage" />
                </div>

                <Button :processing="state.formProcessing"
                      class="bg-blue-500 disabled:bg-blue-300 text-white"
                      type="submit" text="Update" />
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import {useRoute} from 'vue-router'
import router from "@/router"

import {computed, defineComponent, onMounted, reactive} from "vue"
import Request, {ErrorBag} from "@/plugins/Request"
import {displayDate, objectToFormData, saveFileFromStream} from "@/compositions/Utils"

import {
  Resource,
  ResourceForm,
  resourceToFormFactory,
  ResourceType,
  ResourceType as resourceType
} from "@/compositions/Resource"
import Errors from '@/components/DisplayErrors.vue'
import Loading from '@/components/Loading.vue'
import AdminAreaDialogue from '@/components/AdminAreaDialogue.vue'
import Button from '@/components/Button.vue'
import FlashMessage from '@/components/FlashMessage.vue'

export default defineComponent({
  components: {
    Loading, Errors, AdminAreaDialogue, Button, FlashMessage
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
    }

    let resource: Resource = {} as Resource
    let form: ResourceForm = {} as ResourceForm

    let selectedFile: any = null

    let state = reactive({
      id,
      loading: false,
      errorBag,
      resource,
      form,
      selectedFile,
      formProcessing: false,
      flashMessage: null,
      resourceNotFound: false,
    })

    const selectedFileName = computed(() => {

      if (state.selectedFile !== null) {
        return state.selectedFile ? state.selectedFile.name : null
      }

      return null
    })

    function fetchResource(id: number) {
      state.loading = true
      state.errorBag.errors = []
      state.resourceNotFound = false

      let r = new Request()

      r.to('resources.edit', [id])
          .success((res) => {
            assignResourceAndForm(res)
          })
          .error((err) => {
            state.errorBag = err
            if(state.errorBag.statusCode === 404) {
              state.resourceNotFound = true
            }
          })
          .finally(() => state.loading = false)
          .asAdmin()
          .send()
    }

    function assignResourceAndForm(res: any) {
      const r: Resource = res as Resource
      state.resource = r
      state.form = resourceToFormFactory(r, r.type)
    }

    function handleChoseFile(event: any) {
      state.selectedFile = event.target.files[0]
    }

    function updateResource() {
      state.formProcessing = true

      state.errorBag.errors = []
      state.errorBag.message = null

      state.form._method = 'PUT'

      let request = (new Request()).to('resources.update', [state.id]).asAdmin()

      if (state.selectedFile) {
        state.form.file = state.selectedFile

        request.headers({
          'Content-Type' : 'multipart/form-data'
        })
      } else {
        delete state.form.file
      }

      if(state.resource.type === ResourceType.link) {
        state.form.opens_in_new_tab = state.form.opens_in_new_tab ? 1 : 0
      }

      const formData = objectToFormData(state.form)

      request.with(formData)
      request.success(res => {
        assignResourceAndForm(res.resource)
        state.flashMessage = res.message

        state.selectedFile = null
      })
      request.error(err => state.errorBag = err)
      request.finally(() => state.formProcessing = false)
      request.send()
    }

    function downloadCurrentFile() {
      const r = new Request()

      r.to('resources.download',[state.id])
          .asAdmin()
          .success((res) => {
            const saveAs: string = state.resource.title + '.pdf'

            saveFileFromStream([res],saveAs)
          })
          .error((err) => state.errorBag = err )
          .download()
    }

    onMounted(() => {
      fetchResource(state.id)
    })
    return {
      //accessors
      state, resourceType, selectedFileName,

      // funcs
      displayDate, handleChoseFile, updateResource, downloadCurrentFile
    }
  },
})
</script>