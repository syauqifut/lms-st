<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('course-modules.get_by_course',dataCourse.id)"
      >
        {{dataCourse.title}}
      </inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Meeting
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input v-model="form.addAgain" type="hidden">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.title"
            :errors="$page.errors.title"
            class="pr-6 pb-8 w-full lg"
            label="Title Meeting"
            required
          />

          <text-input
            v-model="form.date"
            type="date"
            :errors="$page.errors.date"
            class="pr-6 pb-8 w-full lg"
            label="Date Start"
            required
          />

          <text-input
            v-model="form.schedule_start_at"
            type="time"
            :errors="$page.errors.schedule_start_at"
            class="pr-6 pb-8 w-full lg"
            label="Time Start"
            required
          />
          <text-input
            v-model="form.schedule_end_at"
            type="time"
            :errors="$page.errors.schedule_end_at"
            class="pr-6 pb-8 w-full lg"
            label="Time End"
            required
          />

          <!--
          <select-input
            v-model="form.course_id"
            :errors="$page.errors.course_id"
            class="pr-6 pb-8 w-full lg"
            label="Course"
          >
             <option value selected />
            <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
          </select-input>

          <select-input
            v-model="form.group_id"
            :errors="$page.errors.group_id"
            class="pr-6 pb-8 w-full lg"
            label="Group"
          >
             <option value selected />
            <option v-for="group in groups" :key="group.id" :value="group.id">{{ group.classes }}</option>
          </select-input>

          <select-input
            v-model="form.teacher_id"
            :errors="$page.errors.teacher_id"
            class="pr-6 pb-8 w-full lg"
            label="Guru"
          >
            <option value selected />
            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.first_name }} {{ teacher.last_name }}</option>
          </select-input>
          -->

          <select-input
            v-model="form.is_active"
            :errors="$page.errors.is_active"
            class="pr-6 pb-8 w-full lg"
            label="Status"
            required
          >
            <option value="0">Non Active</option>
            <option value="1" selected>Active</option>
            <!-- <option value="2" selected>Selesai</option>
            <option value="3" selected>Batal</option> -->
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            class="btn-bland mr-2 cursor-pointer"
            @click="addAnother"
          >Create and Add new Meeting</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >
            Create Meeting
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


export default {
  metaInfo: { title: 'Create Learning Module' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,

  },
  remember: 'form',
  props: {
    dataCourse: Object ,
  },
  data() {
    return {
      sending: false,
      form: {
        title: null,
        date: null,
        schedule_start_at: null,
        schedule_end_at: null,
        course_id: null,
        is_active: 0,
        addAgain: false,
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

      var data = new FormData()
      data.append('title', this.form.title || '')
      data.append('date', this.form.date || '')
      data.append('schedule_start_at', this.form.schedule_start_at || '')
      data.append('schedule_end_at', this.form.schedule_end_at || '')
      data.append('course_id', this.dataCourse.id || '')
      data.append('is_active', this.form.is_active || '')
      data.append('addAgain', this.form.addAgain || '')

      this.$inertia.post(this.route('course-modules.store'), data).then(() => this.sending = false)//{
        //this.sending = false,
        //this.form.addAgain = false
        //this.form.is_active = 1
        //this.form.title = null
        //this.form.date = null
        //this.form.schedule_start_at = null
        //this.form.schedule_end_at = null
        //this.form.course_id = null
      //})
    },
  },
}
</script>
