<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('groups')">Group</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
      <span class="text-xl">{{ ' - ' + group.code }}</span>
    </h1>
    <trashed-message v-if="group.is_active == 2" class="mb-6" @restore="restore">
      This Group has been removed.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.classes" :errors="$page.errors.classes" class="pr-6 pb-8 w-full lg:w-1/2" label="Group" />
          <text-input v-model="form.huruf" :errors="$page.errors.huruf" class="pr-6 pb-8 w-full lg:w-1/2" label="Huruf" />
          <text-input v-model="form.year" :errors="$page.errors.year" class="pr-6 pb-8 w-full lg:w-1/2" label="Periode" />
          <select-input v-model="form.academicterms" :errors="$page.errors.academicterms" class="pr-6 pb-8 w-full lg:w-1/2" label="Semester">
            <option :value="null" />
            <option value="1">Odd</option>
            <option value="2">Even</option>
          </select-input>
          <div class="pr-6 pb-8 w-full lg:w-1/2">
            <h1 class="mb-1">Level</h1>
            <v-select label="title" :reduce="levels => levels.id" v-model="form.level_id" :options="levels"></v-select>
          </div>
          <div class="pr-6 pb-8 w-full lg:w-1/2">
            <h1 class="mb-1">Category</h1>
            <v-select label="title" :reduce="categories => categories.id" v-model="form.category_id" :options="categories"></v-select>
          </div>
           <div class="pr-6 pb-8 w-full lg:w-1/2">
            <h1 class="mb-1">Teachers</h1>
            <v-select label="name" :reduce="users => users.id" v-model="form.mainteacher" :options="users"></v-select>
          </div>
          <select-input v-model="form.kel_kelas" :errors="$page.errors.kel_kelas" class="pr-6 pb-8 w-full lg:w-1/2" label="Gender">
            <option :value="null" />
            <option value="Putra">Male</option>
            <option value="Putri">Female</option>
          </select-input>

           <select-input
            v-model="form.is_active"
            :errors="$page.errors.is_active"
            class="pr-6 pb-8 w-full lg:w-1/2"
            label="Status"
          >
            <option value="1" selected>Active</option>
            <option value="0">Not Active</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button v-if="group.is_active == 0 || group.is_active == 1" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Group</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Edit Group</loading-button>
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
  },
  props: {
    group: Object,
    levels: Array,
    users: Array,
    categories : Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.group.classes,
        classes: this.group.classes,
        mainteacher: this.group.mainteacher,
        academicterms: this.group.academicterms,
        year: this.group.year,
        is_teacher:this.$page.auth.user.usertype_id == 3,
        kel_kelas: this.group.kel_kelas,
        level_id: this.group.level_id,
        huruf: this.group.huruf,
        is_active: this.group.is_active,
        category_id: this.group.category_id
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route('groups.update', this.group.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this groups?')) {
        this.$inertia.delete(this.route('groups.destroy', this.group.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this groups?')) {
        this.$inertia.put(this.route('groups.restore', this.group.id))
      }
    },
  },
}
</script>
