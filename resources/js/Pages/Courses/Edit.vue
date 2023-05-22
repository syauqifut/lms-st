<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('courses.index')"
      >
        Learning
      </inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.title }}
    </h1>
    <trashed-message
      v-if="course.is_active == 2"
      class="mb-6"
      @restore="restore"
    >
     This Learning has been deleted .
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.title"
            :errors="$page.errors.title"
            class="pr-6 pb-8 w-full lg"
            label="Title Learning"
          />

          <textarea-input
            v-model="form.description"
            :errors="$page.errors.description"
            class="pr-6 pb-8 w-full lg"
            label="Description"
          />

          <text-input
            v-model="form.join_code"
            :errors="$page.errors.join_code"
            class="pr-6 pb-8 w-full lg"
            label="Join code"
            disabled
            readonly
          />

          <file-input
            v-model="form.photo"
            :errors="$page.errors.photo"
            class="pr-6 pb-8 w-full lg"
            type="file"
            accept="image/*"
            label="Image"
          />

          <img v-if="course.photo" class="block w-full h-full ml-4" :src="img_src">

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Subject</h1>
            <v-select label="fullname" :reduce="subjects => subjects.id" v-model="form.subject_id" :options="subjects"></v-select>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Category</h1>
            <v-select label="title" :reduce="categories => categories.id" v-model="form.category_id" :options="categories"></v-select>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Group</h1>
            <v-select  v-if="($page.auth.user.usertype_id  == 1 || $page.auth.user.usertype_id  == 4)" label="fullname" :reduce="groups => groups.id" v-model="form.group_id" :options="groups" ></v-select>
             <v-select v-else label="fullname" :reduce="groups => groups.id" v-model="form.group_id" :options="groups" disabled
            readonly></v-select>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Teacher</h1>
            <v-select label="fullname" :reduce="teachers => teachers.id" v-model="form.teacher_id" :options="teachers"></v-select>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Level</h1>
            <v-select label="title" :reduce="levels => levels.id" v-model="form.level_id" :options="levels"></v-select>
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
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button
            v-if="(course.is_active == 0 || course.is_active == 1) && ($page.auth.user.usertype_id  == 1 || $page.auth.user.usertype_id  == 4)"
            class="text-red-600 hover:underline"
            tabindex="-1"
            type="button"
            @click="destroy"
          >
            Delete Learning
          </button>
          <loading-button
            :loading="sending"
            class="btn-indigo ml-auto"
            type="submit"
          >
            Update Learning
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
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo: { title: 'Edit Learning' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    FileInput,
    TrashedMessage,
  },
  remember: 'form',
  props: {
    subjects: Array,
    categories: Array,
    levels: Array,
    teachers: Array,
    groups: Array,
    course: Object,
  },
  data() {
    return {
      sending: false,
      img_src: '/images/courses/' + this.course.photo,
      form: {
        title: this.course.title,
        description: this.course.description,
        join_code: this.course.join_code,
        photo: null,
        subject_id: this.course.subject_id,
        category_id: this.course.category_id,
        level_id: this.course.level_id,
        group_id: this.course.group_id,
        teacher_id: this.course.teacher_id,
        is_active: this.course.is_active,
      },
    }
  },
  methods: {
    destroy() {
      if (confirm('Are you sure you want to delete this learning?')) {
        this.$inertia.delete(this.route('courses.destroy', this.course.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this course?')) {
        this.$inertia.put(this.route('courses.restore', this.course.id))
      }
    },
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('title', this.form.title || '')
      data.append('description', this.form.description || '')
      data.append('photo', this.form.photo || '')
      data.append('subject_id', this.form.subject_id || '')
      data.append('category_id', this.form.category_id || '')
      data.append('level_id', this.form.level_id || '')
      data.append('group_id', this.form.group_id || '')
      data.append('teacher_id', this.form.teacher_id || '')
      data.append('is_active', this.form.is_active || '')
      data.append('_method', 'put')

      this.$inertia
        .post(this.route('courses.update', this.course.id), data)
        .then(() => {
          this.sending = false
          this.form.photo = null
        })
    },
  },
}
</script>
