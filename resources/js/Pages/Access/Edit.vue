<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route(routes+'.list')">{{info}}</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full" label="Menu Name" />
          <text-input v-model="form.icon" :errors="$page.errors.icon" class="pr-6 pb-8 w-full lg:w-1/2" label="Icon" />
          <span class="pr-6 pb-8 w-full lg:w-1/2 flex justify-center items-center " ><i :class="form.icon" style="font-size:30px;"></i></span>
          <text-input v-model="form.route" :errors="$page.errors.route" class="pr-6 pb-8 w-full lg:w-1/2" label="Route" />
          <textarea-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full" label="Description" />

        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete {{info}}</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update {{info}}</loading-button>
        </div>
      </form>
    </div>
    
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import TextareaInput from '@/Shared/TextareaInput'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    TextareaInput
  },
  props: {
    menu: Object,
  },
  remember: 'form',
  data() {
    return {
      info: "Access",
      routes: "access",
      sending: false,
      form: {
        name: this.menu.name,
        icon: this.menu.icon,
        route: this.menu.route,
        description: this.menu.description,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route(this.routes+'.update', this.menu.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this access?')) {
        this.$inertia.delete(this.route(this.routes+'.destroy', this.menu.id))
      }
    },
  },
}
</script>
