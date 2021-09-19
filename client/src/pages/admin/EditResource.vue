<template>
  <div class="w-full">
    Welcome to admin page.

    <Loading v-if="state.loading"/>

    <div v-else>
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                hello
            </div>
          </div>
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

import { Resource } from "@/compositions/Resource";
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

    let state = reactive({
      id,
      loading: false,
      errorBag,
      resource,
    });

    function fetchResource(id : number) {
        state.loading = true

        let r = new Request()

          r.to('resources.edit',[id])
          .success((res) => {
              state.resource = res.data as Resource
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
      state,

      // funcs
      displayDate,
    };
  },
});
</script>