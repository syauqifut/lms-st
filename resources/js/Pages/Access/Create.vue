<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('access.list')">{{info}}</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit" ref="form">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full" label="Menu Name" />
          <text-input v-model="form.logo" :errors="$page.errors.logo" class="pr-6 pb-8 w-full lg:w-1/2" label="Icon" />
          <span class="pr-6 pb-8 w-full lg:w-1/2 flex justify-center items-center " ><i :class="form.logo" style="font-size:30px;"></i></span>
          <text-input v-model="form.route" :errors="$page.errors.route" class="pr-6 pb-8 w-full lg:w-1/2" label="Route" />
          <textarea-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full" label="Description" />

        </div>
       
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <input type="hidden" v-model="form.addAgain" :errors="$page.errors.year" class="pr-6 pb-8 w-full lg:w-1/2" />
          <span v-on:click="newaa" class="btn-bland mr-2">Create {{info}} and add more</span>
          <loading-button ref="sbbtn" :loading="sending" class="btn-indigo" type="submit">Create {{info}}</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TextareaInput from '@/Shared/TextareaInput'

export default {
  metaInfo: { title: 'Access' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput
  },
  props: {
    listMenus: Array,
  },
  remember: 'form',
  data() {
    return {
      info: "Access",
      sending: false,
      form: {
        name: null,
        icon: 'fa fa-bars',
        route: null,
        parentId: 0,
        description: null,
        addAgain: false,
      },
    }
  },
  methods: {
    newaa(){
      this.form.addAgain = true
      const btn = this.$refs.sbbtn.$el
      btn.click()
    },
    submit() {
      this.sending = true
      this.$inertia.post(this.route('access.store'), this.form)
        .then(() => 
        {
          this.sending = false
          this.form.name = null
          this.form.logo = ''
          this.form.route = null
          this.form.description = null
        })
    },
  },
}
</script>
