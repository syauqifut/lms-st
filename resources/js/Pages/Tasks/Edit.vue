<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tasks.index', task.course_module_id)">Task</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <!-- <trashed-message v-if="level.deleted_at" class="mb-6" @restore="restore">
      Task ini telah dihapus.
    </trashed-message> -->
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.name"
            :errors="$page.errors.name"
            class="pr-6 pb-8 w-full lg"
            label="Task Name"
          />
          <text-input
            v-model="form.date"
            type="date"
            :errors="$page.errors.date"
            class="pr-6 pb-8 w-full lg"
            label="Date"
          />

          <select-input
            v-model="form.task_type"
            :errors="$page.errors.task_type"
            class="pr-6 pb-8 w-full lg"
            label="Task Type"
          >
            <option value="Tugas">Tugas</option>
            <option value="UH">UH</option>
            <option value="UTS">UTS</option>
            <option value="UAS">UAS</option>
            <option value="Perform">Perform</option>
          </select-input>

          <text-input
            v-model="form.due_date"
            type="date"
            :errors="$page.errors.due_date"
            class="pr-6 pb-8 w-full lg"
            label="Submission Deadline"
          />

          <select-input
            v-if="userLogin.usertype_id != 3"
            v-model="form.teacher_id"
            :errors="$page.errors.teacher_id"
            class="pr-6 pb-8 w-full lg"
            label="Teacher"
          >
            <option value="" selected />
            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.fullname }}</option>
          </select-input>

          <select-input
            v-model="form.is_file"
            :errors="$page.errors.is_file"
            class="pr-6 pb-8 w-full lg"
            label="in the form of a file"
          >
            <option value="0" selected>Not exist</option>
            <option value="1">Exist</option>
            <option value="2">Short Questions</option>
          </select-input>

          <a v-if="task.file" :href="route('tasks.download_file', task.id)" class="text-blue-500 mb-5 text-lg hover:underline" target="_blank">{{ task.file }}</a>

          <text-input
            v-if="form.is_file == 0"
            v-model="form.link"
            :errors="$page.errors.link"
            class="pr-6 pb-8 w-full lg"
            label="Link Google Form"
          />

           <textarea-input
            v-if="form.is_file == 2"
            v-model="form.soal"
            :errors="$page.errors.soal"
            class="pr-6 pb-8 w-full lg"
            label="Short Questions"
          />

          <!-- <select-input
            v-if="form.is_file == 0"
            v-model="form.auto_mark"
            :errors="$page.errors.auto_mark"
            class="pr-6 pb-8 w-full lg"
            label="Penilaian Otomatis"
          >
            <option value="0" selected>Manual</option>
            <option value="1">Otomatis</option>
          </select-input> -->

          <!-- <select-input
            v-if="form.is_file == 0"
            v-model="form.random_order"
            :errors="$page.errors.random_order"
            class="pr-6 pb-8 w-full lg"
            label="Tampil Secara Acak"
          >
            <option value="0" selected>Acak</option>
            <option value="1">Urut</option>
          </select-input> -->

          <file-input
            v-if="form.is_file == 1"
            v-model="form.file"
            :errors="$page.errors.file"
            class="pr-6 pb-8 w-full lg"
            type="file"
            label="File"
          />
        </div>

        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Task</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Task</loading-button>
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
import moment from 'moment'

export default {
  metaInfo() {
    return {
      title: `${this.form.title}`,
    }
  },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
    TextareaInput,
    TrashedMessage,
  },
  props: {
    userLogin: Object,
    task: Object,
    teachers: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      img_src: '/files/tasks/' + this.task.file,
      form: {
        name: this.task.name,
        date: this.task.date,
        task_type: this.task.task_type,
        teacher_id: this.task.teacher_id,
        due_date: this.formatDate(this.task.due_date),
        is_file: this.task.is_file,
        auto_mark: this.task.auto_mark,
        random_order: this.task.random_order,
        link: this.task.link,
        soal: this.task.soal,
      },
    }
  },
  methods: {
    formatDate(date) {
      return moment(date).format('YYYY-MM-DD')
    },
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('teacher_id', this.form.teacher_id || this.task.teacher_id)
      data.append('date', this.form.date || this.task.date)
      data.append('name', this.form.name || this.task.name)
      data.append('task_type', this.form.task_type || this.task.task_type)
      data.append('due_date', this.form.due_date || this.task.due_date)
      data.append('is_file', this.form.is_file || this.task.is_file)
      data.append('file', this.form.file || '')
      data.append('auto_mark', this.form.auto_mark || this.task.auto_mark)
      data.append('random_order', this.form.random_order || this.task.random_order)
      data.append('link', this.form.link || this.task.link)
      data.append('soal', this.form.soal || this.task.soal)
      data.append('_method', 'put')

      this.$inertia
        .post(this.route('tasks.update', this.task.id), data)
        .then(() => {
          this.sending = false
        })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this task? Deleting a task also permanently deletes files')) {
        this.$inertia.delete(this.route('tasks.destroy', this.task.id))
      }
    },
  },
}
</script>
