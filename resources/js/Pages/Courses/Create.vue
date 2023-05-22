<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('courses.index')"
      >
        Learning
      </inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Learning
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input v-model="form.addAgain" type="hidden">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.title" :errors="$page.errors.title" class="pr-6 pb-8 w-full lg" label="Title Learning" />

          <textarea-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full lg" label="Description" />

         <!-- <file-input v-model="form.photo" :errors="$page.errors.photo" class="pr-6 pb-8 w-full lg" type="file" accept="image/*" label="Image" />-->

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Subject</h1>
            <v-select v-model="form.subject_id" label="fullname" :reduce="subjects => subjects.id" :options="subjects"></v-select>
            <div v-if="$page.errors.subject_id" class="form-error">{{ $page.errors.subject_id[0] }}</div>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Category</h1>
            <v-select v-model="form.category_id" label="title" :reduce="categories => categories.id" :options="categories"></v-select>
            <div v-if="$page.errors.category_id" class="form-error">{{ $page.errors.category_id[0] }}</div>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Group</h1>
            <v-select v-model="form.group_id" label="fullname" :reduce="groups => groups.id" :options="groups"></v-select>
            <div v-if="$page.errors.group_id" class="form-error">{{ $page.errors.group_id[0] }}</div>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Teacher</h1>
            <v-select v-model="form.teacher_id" label="fullname" :reduce="teachers => teachers.id" :options="teachers"></v-select>
            <div v-if="$page.errors.teacher_id" class="form-error">{{ $page.errors.teacher_id[0] }}</div>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Level</h1>
            <v-select v-model="form.level_id" label="title" :reduce="levels => levels.id" :options="levels"></v-select>
            <div v-if="$page.errors.level_id" class="form-error">{{ $page.errors.level_id[0] }}</div>
          </div>

          <select-input
            v-model="form.is_active"
            :errors="$page.errors.is_active"
            class="pr-6 pb-8 w-full lg"
            label="Status"
          >
            <option value="1" selected>Active</option>
            <option value="0">Not Active</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            class="btn-bland mr-2 cursor-pointer"
            @click="addAnother"
          >Create and Add new Learning</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >
            Create Learning
          </loading-button>
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
import FileInput from '@/Shared/FileInput'

export default {
  metaInfo: { title: 'Create Learning' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    FileInput,
  },
  props: {
    subjects: Array,
    teachers: Array,
    groups: Array,
    categories: Array,
    levels: Array,
  },
  remember: 'form',
  data() {
    return {
      role: {
        is_teacher: this.$page.auth.user.usertype_id == 3,
      },
      sending: false,
      form: {
        title: null,
        description: null,
        photo: null,
        subject_id: null,
        category_id: null,
        level_id: null,
        is_active: 1,
        addAgain: false,
        teacher_id: this.$page.auth.user.usertype_id == 3 ? this.$page.auth.user.id : null,
      },
    }
  },
  methods: {
    addAnother() {
      this.form.addAgain = true
      const btn = this.$refs.btnsubmit.$el
      btn.click()
    },
    submit() {
      this.sending = true

      var data = new FormData
      data.append('title', this.form.title || '')
      data.append('description', this.form.description || '')
      data.append('photo', this.form.photo || '')
      data.append('subject_id', this.form.subject_id || '')
      data.append('category_id', this.form.category_id || '')
      data.append('level_id', this.form.level_id || '')
      data.append('is_active', this.form.is_active || '')
      data.append('group_id', this.form.group_id || '')
      data.append('teacher_id', this.form.teacher_id || '')
      data.append('addAgain', this.form.addAgain || '')

      this.$inertia.post(this.route('courses.store'), data).then(() => {
        this.sending = false
        this.form.addAgain = false
      })
    },
  },
}
</script>
