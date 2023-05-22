<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('groups')">Groups</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit" ref="form">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.classes" :errors="$page.errors.classes" class="pr-6 pb-8 w-full lg:w-1/2" label="Groups" />
          <text-input type="number" v-model="form.year" :errors="$page.errors.year" class="pr-6 pb-8 w-full lg:w-1/2" label="Year" />
          <input type="hidden" v-model="form.addAgain" :errors="$page.errors.year" class="pr-6 pb-8 w-full lg:w-1/2" />
          <select-input v-model="form.academicterms" :errors="$page.errors.academicterms" class="pr-6 pb-8 w-full lg:w-1/2" label="Academic Terms">
            <option :value="null" />
            <option value="1">Odd</option>
            <option value="2">Even</option>
          </select-input>
          <select-input v-model="form.mainteacher" :errors="$page.errors.mainteacher" class="pr-6 pb-8 w-full lg:w-1/2" label="Main Teacher">
            <option :value="null" />
            <option v-for="user in users" :key="user.id"  :value="user.id">{{user.name}}</option>
          </select-input>
        </div>
       
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span v-on:click="newaa" class="btn-bland mr-2">Create and add new Groups</span>
          <loading-button ref="sbbtn" :loading="sending" class="btn-indigo" type="submit">Create Groups</loading-button>
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

export default {
  metaInfo: { title: 'Create Groups' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
  },
  props: {
    users: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        classes: null,
        year: '2020',
        academicterms: null,
        addAgain: false,
        mainteacher: null,
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
      this.$inertia.post(this.route('groups.store'), this.form)
        .then(() => 
        {
          this.sending = false
          this.form.classes = null
          this.form.year = '2020'
          // this.form.academicterms = null
          this.form.addAgain = false
          // this.form.mainteacher = null
        })
    },
  },
}
</script>
