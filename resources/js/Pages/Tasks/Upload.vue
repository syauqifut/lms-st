<template>
  <div>
    <inertia-link :href="route('tasks.index', task.course_module_id)">
      <h1 class="mb-8 font-bold text-3xl text-blue-800 hover:text-purple-800"> {{ task.name }}</h1>
    </inertia-link>

    <div v-if="datenow2 > task.due_date" class="bg-red-200 relative text-red-500 py-3 px-3 rounded-lg m-2" role="alert">
      <!-- <p class="font-bold">Tidak Bisa Upload Task</p> -->
      <p>Exceeded the deadline for collection <span class="font-bold">{{ task.due_date }} </span></p>
    </div>
    <div v-else></div>

    <div v-if="taskFile" class="bg-white rounded shadow overflow-hidden w-full m-2">
      <div class="m-5">
        <h1>Short Task : {{task.soal}}</h1>
        <h1>
          FILE :
          <a :href="route('tasks.download_uploaded_task', taskFile.id)" target="_blank" class="text-indigo-400 hover:text-indigo-600 hover:underline">
            {{ taskFile.file }}
          </a>
        </h1>
        
        <h1>Description : {{ taskFile.description }}</h1>
        <h1>Collecting time : {{ taskFile.tanggal_kumpul ? taskFile.tanggal_kumpul : 'Havent collected yet'}}</h1>
        <h1>Status : {{ taskFile.status == 0 ? 'Not Seen' : 'Already Viewed' }}</h1>
        <h1>Mark : {{ taskFile.mark ? taskFile.mark : 'No value yet' }}</h1>
        <h1>due date : {{ task.due_date }}</h1>
        <!-- time :{{ currenttime }} -->
      </div>
    </div>

    <div class="lg:flex justify-evenly">
      <div v-if="datenow2 >= task.date" class="flex-1 m-2">
        <form @submit.prevent="submit">
          <div class="bg-white rounded shadow overflow-hidden w-full">
            <div class="flex w-full h-64 items-center justify-center bg-grey-lighter">
              <label class="w-64 flex flex-col items-center px-4 py-6 bg-gray-200 text-blue rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-blue hover:text-white">
                <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                </svg>
                <span v-if="!form.file" class="mt-2 text-base leading-normal">Select a file</span>
                <span v-else class="mt-2 font-bold text-teal-300 leading-normal">File Ready</span>
                <file-input v-model="form.file" type="file" class="hidden" name="file" :errors="$page.errors.file" />
              </label>
            </div>
          </div>
          <div class=" bg-white rounded shadow overflow-hidden p-6">
            <div class="h-full">
              <textarea-input
                v-model="form.description"
                :errors="$page.errors.description"
                class="pr-6 pb-8 w-full lg"
                label="Task Description"
              />
            </div>
            <div class="flex justify-end items-center">
              <loading-button :loading="sending" class="btn-indigo" type="submit">Upload</loading-button>
            </div>
          </div>
        </form>
      </div>

      <div v-else-if="datenow2 < task.due_date && datenow2 < task.date" class="bg-blue-100 w-full border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
        <p class="font-bold">Can't Upload Task</p>
        <p>Assignments can be accessed on {{ task.date }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextareaInput from '@/Shared/TextareaInput'
import FileInput from '@/Shared/FileInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  metaInfo: { title: 'Tasks' },
  layout: Layout,
  components: {
    TextareaInput,
    LoadingButton,
    FileInput,
  },
  props: {
    task: Object,
    taskFile: Object,
  },
  data() {
    return {
      currenttime: '',
      datenow:'',
      datenow2:'',
      tyear:'',
      tmonth:'',
      tdate:'',
      name: 'Upload Task',
      sending: false,
      form: {
        file: null,
        description: null,
      },
    }
  },
  created() {
    setInterval(this.getNow, 300000)
    this.getNow()
  },
  methods: {
    getNow: function() {
      const today = new Date()
      const date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate()
      const time = today.getHours() + ':' + today.getMinutes() + ':' + today.getSeconds()
      const dateTime = date +' '+ time
      this.currenttime = dateTime

      this.datenow=date.split("-")
      this.tyear=this.datenow[0]
      // this.tmonth=this.datenow[1]
      if(this.datenow[1]<10){
        this.tmonth='0'+this.datenow[1]
      }else{
        this.tmonth=this.datenow[1]
      }
      if(this.datenow[2]<10){
        this.tdate='0'+this.datenow[2]
      }else{
        this.tdate=this.datenow[2]
      }

     this.datenow2 = this.tyear +'-'+this.tmonth+'-'+this.tdate + ' 00:00:00'
      this.tgldue = task.due_date
    },
    submit() {
      this.sending = true
      var data = new FormData()
      data.append('soal', this.form.soal || '')
      data.append('file', this.form.file || '')
      data.append('description', this.form.description || '')
      data.append('task_id', this.task.id || '')
      data.append('task_file_id', this.taskFile ? this.taskFile.id : '')
      this.$inertia.post(this.route('tasks.student_upload'), data)
        .then(() => {
          this.sending = false
          this.form.file = null
          this.form.description = null
        })
    },
  },
}
</script>
