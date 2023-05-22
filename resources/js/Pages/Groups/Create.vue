<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('groups')">Group</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl overflow-visible">
      <form ref="form" @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.classes" :errors="$page.errors.classes" class="pr-6 pb-8 w-full lg:w-1/2" label="Group" />
          <text-input v-model="form.huruf" :errors="$page.errors.huruf" class="pr-6 pb-8 w-full lg:w-1/2" label="Alphabet" />
          <text-input v-model="form.year" placeholder="2020-2021" :errors="$page.errors.year" class="pr-6 pb-8 w-full lg:w-1/2" label="Year" />
          <input v-model="form.addAgain" type="hidden" :errors="$page.errors.year" class="pr-6 pb-8 w-full lg:w-1/2">
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
              <option value="Pai">Male-Female</option>
            </select-input>

          <div class="pr-6 pb-8 w-full lg:w-1/2">
            <h1 class="mb-3">Group (copy students from an existing group)</h1>
            <v-select label="classes" :reduce="groups => groups.id" v-model="form.group_id" :options="groups"></v-select>
          </div>
        </div>

        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <!-- <span v-on:click="newaa" class="btn-bland mr-2">Buat Groups dan tambah baru</span> -->
          <!-- <loading-button ref="sbbtn" :loading="sending" class="btn-indigo mr-2" type="submit">Buat Groups</loading-button> -->
          <div class="dropdown inline-block relative">
            <div class="flex">
              <!-- <span class="cursor-pointer rounded-t mr-2 bg-gray-200 btn-bland py-2 px-4 block whitespace-no-wrap" v-on:click="newaa" >Buat baru</span> -->

              <loading-button ref="sbbtn" :loading="sending" class="btn-indigo mr-0 rounded-r-none inline-flex items-center pr-2">
                <span>Create</span>
              </loading-button>
              <div class="">
                <span v-b-toggle.collapse-2 class="px-2 btn-indigo rounded inline-flex items-center cursor-pointer ml-0 rounded-l-none"> 🠋</span>
                <b-collapse id="collapse-2" class="mt-2 absolute text-gray-700 pt-1 right-0 left-auto">
                  <span class="cursor-pointer rounded-t bg-gray-200 btn-bland py-2 px-4 block whitespace-no-wrap" @click="newaa">Create and Add New</span>
                </b-collapse>
              </div>
            </div>
            <!-- <ul class="dropdown-content absolute text-gray-700 pt-1 right-0 left-auto" >
                <li><span class="cursor-pointer rounded-t bg-gray-200 btn-bland py-2 px-4 block whitespace-no-wrap" v-on:click="newaa" >Buat dan Tambah baru</span></li>
              </ul> -->
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<style>
/* .dropdown:hover > .dropdown-content {
	display: block;
} */
</style>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'

export default {
  metaInfo: { title: 'Create Group' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
  },
  props: {
    users: Array,
    levels: Array,
    groups: Array,
    categories: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        classes: null,
        year: null,
        academicterms: null,
        addAgain: false,
        kel_kelas: null,
        level_id: null,
        group_id: null,
        huruf: null,
        is_teacher:this.$page.auth.user.usertype_id == 3,
        mainteacher: this.$page.auth.user.usertype_id == 3 ? this.$page.auth.user.id : null,
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
          // this.form.classes = null
          this.form.year = '2020'
          // this.form.academicterms = null
          this.form.addAgain = false
          // this.form.mainteacher = null
        })
    },
  },
}
</script>
