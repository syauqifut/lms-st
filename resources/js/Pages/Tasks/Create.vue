<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tasks.index', courseModule.id)">Materi</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Task
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <input v-model="form.addAgain" type="hidden">

          <select-input
            v-model="form.create_task"
            :errors="$page.errors.create_task"
            class="pr-6 pb-8 w-full lg"
            label="Task Creation Type"
          >
            <option value="new" selected>Create New Task</option>
            <option value="old">Choose From Old Tasks</option>
          </select-input>

          <div v-if="form.create_task == 'old'" class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Tasks</h1>
            <v-select v-model="form.task_id" label="name" :reduce="tasks => tasks.id" :options="tasks" />
          </div>


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
            v-if="form.create_task == 'new'"
            v-model="form.is_file"
            :errors="$page.errors.is_file"
            class="pr-6 pb-8 w-full lg"
            label="in the form of"
          >
            <option value="0" selected>Link</option>
            <option value="1">File</option>
            <option value="2">Short task</option>
          </select-input>

          <text-input
            v-if="form.is_file == 0"
            v-model="form.link"
            :errors="$page.errors.link"
            class="pr-6 pb-8 w-full lg"
            label="Link Google Form"
          />

          <file-input
            v-if="form.is_file == 1"
            v-model="form.file"
            :errors="$page.errors.file"
            class="pr-6 pb-8 w-full lg"
            type="file"
            label="File"
          />

           <textarea-input
            v-if="form.is_file == 2"
            v-model="form.soal"
            :errors="$page.errors.soal"
            class="pr-6 pb-8 w-full lg"
            label="Short task"
          />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <!-- <span
            class="btn-bland mr-2 cursor-pointer"
            @click="addAnother"
          >Buat Task dan Tambah Baru</span> -->
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >
            Create Task
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
import FileInput from '@/Shared/FileInput'
import TextareaInput from '@/Shared/TextareaInput'

export default {

  metaInfo: { title: 'Create Task' },
  layout: Layout,
  components: {
    
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
    TextareaInput,
  },
  props: {
    courseModule: Object,
    teachers: Array,
    userLogin: Object,
    tasks: Array,
  },
  data() {
    return {
      sending: false,
      form: {
        create_task: 'new',
        // name: null,
        is_file: null,
        addAgain: false,
        // auto_mark: 1,
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
      data.append('course_module_id', this.courseModule.id)
      data.append('teacher_id', this.form.teacher_id || '')
      data.append('date', this.form.date || '')
      data.append('name', this.form.name || '')
      data.append('task_type', this.form.task_type || '')
      data.append('due_date', this.form.due_date || '')
      data.append('is_file', this.form.is_file || '')
      data.append('file', this.form.file || '')
      data.append('soal',this.form.soal || '')
      data.append('auto_mark', this.form.auto_mark || '')
      data.append('random_order', this.form.random_order || '')
      data.append('link', this.form.link || '')
      data.append('create_task', this.form.create_task || '')
      data.append('task_id', this.form.task_id || '')
      data.append('addAgain', this.form.addAgain || '')

      this.$inertia.post(this.route('tasks.store'), data).then(() => {
        this.form.addAgain = false
        // this.form.is_file = null
        this.sending = false
      })
    },
  },

}
</script>