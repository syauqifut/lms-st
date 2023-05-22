<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('course-modules.get_by_course',courseModules.course_id)"
      >
        {{ courseModules.title }}
      </inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Edit Meeting
    </h1>
    <trashed-message
      v-if="!courseModules.is_active"
      class="mb-6"
      @restore="restore"
    >
      This meeting has been disabled.
    </trashed-message>
    <div class="lg:flex">
      <div class="flex-1 mb-5">
        <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
          <form @submit.prevent="submit">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <text-input
                v-model="form.title"
                :errors="$page.errors.title"
                class="pr-6 pb-8 w-full lg"
                label="Title Meeting"
              />

              <text-input
                v-model="form.date"
                type="date"
                :errors="$page.errors.date"
                class="pr-6 pb-8 w-full lg"
                label="Date Start"
              />

              <text-input
                v-model="form.schedule_start_at"
                type="time"
                :errors="$page.errors.schedule_start_at"
                class="pr-6 pb-8 w-full lg"
                label="Time Start"
              />

              <text-input
                v-model="form.schedule_end_at"
                type="time"
                :errors="$page.errors.schedule_end_at"
                class="pr-6 pb-8 w-full lg"
                label="Time End"
              />
            <!--
              <select-input
                v-model="form.course_id"
                :errors="$page.errors.subject_id"
                class="pr-6 pb-8 w-full lg"
                label="Course"
              >
                 <option value selected />
                <option
                  v-for="course in courses"
                  :key="course.id"
                  :value="course.id"
                >
                  {{ course.title }}
                </option>
              </select-input>

              <select-input
                v-model="form.group_id"
                :errors="$page.errors.group_id"
                class="pr-6 pb-8 w-full lg"
                label="Group"
              >
                <option value selected />
                <option
                  v-for="group in groups"
                  :key="group.id"
                  :value="group.id"
                >
                  {{ group.classes }}
                </option>
              </select-input>

              <select-input
                v-model="form.teacher_id"
                :errors="$page.errors.teacher_id"
                class="pr-6 pb-8 w-full lg"
                label="Guru"
              >
                <option value selected />
                <option
                  v-for="teacher in teachers"
                  :key="teacher.id"
                  :value="teacher.id"
                >
                  {{ teacher.first_name }} {{ teacher.last_name }}
                </option>
              </select-input>
                -->
              <select-input
                v-model="form.is_active"
                :errors="$page.errors.is_active"
                class="pr-6 pb-8 w-full lg"
                label="Status"
              >
                <option value="0">Non Active</option>
                <option value="1" selected>Active</option>
                <!-- <option value="2" selected>Selesai</option>
                <option value="3" selected>Batal</option> -->
              </select-input>
            </div>
            <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
              <!-- <button
                v-if="courseModules.is_active"
                class="text-red-600 hover:underline"
                tabindex="-1"
                type="button"
                @click="destroy"
              >
                Delete Meeting
              </button> -->
              <loading-button
                :loading="sending"
                class="btn-indigo ml-auto"
                type="submit"
              >
                Update Meeting
              </loading-button>
            </div>
          </form>
        </div>
      </div>
      <!--<div class="flex-1 lg:mt-0 mt-3">
        <h1 class="mb-8 font-bold text-3xl">File Materi</h1>
        <div class="bg-white mx-auto p-6">
          <h1>hello</h1>
          <form>
            <form @submit.prevent="submitCourseUnit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input
                  v-model="form.web_url"
                  :errors="$page.errors.web_url"
                  class="pr-6 pb-8 w-full lg"
                  label="URL Web"
                />
                <file-input
                  v-model="form.file_name"
                  :errors="$page.errors.file_name"
                  class="pr-6 pb-8 w-full lg"
                  type="file"

                  label="File (PDF/Word/PPT)"
                />

                <text-input
                  v-model="form.vidcon_link"
                  :errors="$page.errors.vidcon_link"
                  class="pr-6 pb-8 w-full lg"
                  label="Link Video Conference"
                />
                <select-input
                  v-model="form.link_to_unit_id"
                  :errors="$page.errors.link_to_unit_id"
                  class="pr-6 pb-8 w-full lg"
                  label="Link To Unit"
                >
                  <option
                    v-for="modules in courseModules"
                    :key="modules.id"
                    :value="modules.id"
                  >
                    {{ modules.title }}
                  </option>
                </select-input>
                <select-input
                  v-model="form.unit_is_active"
                  :errors="$page.errors.unit_is_active"
                  class="pr-6 pb-8 w-full lg"
                  label="Status"
                >
                  <option value="0">Tidak Aktif</option>
                  <option value="1" selected>Aktif</option>
                </select-input>
                <loading-button
                  :loading="sending"
                  class="btn-indigo ml-auto"
                  type="submit"
                >
                  Insert Materi
                </loading-button>
              </div>
            </form>
          </form>
        </div>
      </div> -->
    </div>
    <!--
    <div class="bg-white rounded shadow overflow-x-auto mt-10 mb-10">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Web URL</th>
          <th class="px-6 pt-6 pb-4">File</th>
          <th class="px-6 pt-6 pb-4">Video Conference Link</th>
          <th class="px-6 pt-6 pb-4">Status</th>
          <th class="px-6 pt-6 pb-4" />
        </tr>
        <tr
          v-for="unit in courseUnits.data"
          :key="unit.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <a :href="unit.web_url" target="_blank">
              {{ unit.web_url }}
            </a>
          </td>
          <td class="border-t">
            {{ unit.file_name }}
          </td>
          <td class="border-t">
            {{ unit.vidcon_link }}
          </td>
          <td class="border-t w-px">
            {{ unit.is_active ? 'Aktif' : 'Tidak Aktif' }}
          </td>
          <td class="border-t w-px">
            <inertia-link
              class="px-4 flex items-center"

              tabindex="-1"
            >
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="courseUnits.data.length === 0">
          <td class="border-t px-7 py-4 text-center" colspan="6">Tidak ada course unit di modul ini.</td>
        </tr>
      </table>
    </div>
    -->
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import FileInput from '@/Shared/FileInput'
import Icon from '@/Shared/Icon'

import moment from 'moment'

export default {
  metaInfo: { title: 'Edit Learning Module' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    FileInput,
    Icon,
  },
  remember: 'form',
  props: {
    courseModules: Object,
  },

  data() {
    return {
      sending: false,
      form: {
        title: this.courseModules.title,
        date: this.courseModules.date,
        schedule_start_at: this.courseModules.schedule_start_at,
        schedule_end_at: this.courseModules.schedule_end_at,
        course_id: this.courseModules.course_id,
        is_active: this.courseModules.is_active,
        course_module_id: this.courseModules.id,
      },
    }
  },
  methods: {
    dateTimeLocal(date) {
      return moment(date, 'YYYY-MM-DD h:m:s').format('YYYY-MM-DDThh:mm')
    },
    destroy() {
      if (confirm('Are you sure you want to deactivate this meeting?')) {
        this.$inertia.delete(
          this.route('course-modules.destroy', this.courseModules.id)
        )
      }
    },
    restore() {
      if (confirm('Are you sure you want to activate this meeting?')) {
        this.$inertia.put(this.route('course-modules.restore', this.courseModules.id))
      }
    },

    submit() {
      this.sending = true

      var data = new FormData()
      data.append('title', this.form.title || '')
      data.append('date', this.form.date || '')
      data.append('schedule_start_at', this.form.schedule_start_at || '')
      data.append('schedule_end_at', this.form.schedule_end_at || '')
      data.append('course_id', this.form.course_id || '')
      data.append('is_active', this.form.is_active || '')

      data.append('_method', 'put')

      this.$inertia
        .post(this.route('course-modules.update', this.courseModules.id), data)
        .then(() => {
          this.sending = false
        })
    },
  },
}
</script>
