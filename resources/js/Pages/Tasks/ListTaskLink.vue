<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tasks.index', task.course_module_id)">{{ task.name }}</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> List of Student Assignments Link
    </h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <!-- <inertia-link class="btn-indigo" :href="route('levels.create')">
        <span>Buat</span>
        <span class="hidden md:inline">Level</span>
      </inertia-link> -->
    </div>

    <div class="mb-6 flex justify-between items-center overflow-x-auto">
      <!-- <a :href="route('tasks.download_zip', task.id)" class="btn-indigo" target="_blank">Download ZIP</a> -->
      <a :href="route('export-task-mark', task.id)" class="btn-indigo" target="_blank">Template Mark</a>
      <a :href="route('import-task-mark')+'?task_id='+task.id" class="btn-indigo">Upload Mark</a>
      <button class="btn-indigo" data-toggle="tooltip" title="Rate All" @click="toggleModal('Task Rate All', '', task.id)">
        Rate All
      </button>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
     <table class="w-full no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">User</th>
          <th class="px-6 pt-6 pb-4">Description</th>
          <th class="px-6 pt-6 pb-4">Status</th>
          <th class="px-6 pt-6 pb-4">Mark</th>
          <th class="px-6 pt-6 pb-4">Collection Time</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr
          v-for="taskFile in taskFiles.data"
          :key="taskFile.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
            >
              {{ taskFile.fullname }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
            >
              {{ taskFile.description }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
            >
              {{ taskFile.status }}
            </inertia-link>
          </td>
          <td class="border-t">
           <div v-if="taskFile.mark>=0">
             
                {{  taskFile.mark>=0? taskFile.mark : 'Not rated yet' }}  
                   
          </div>
          
          <div v-else>
            {{  taskFile.mark? '' : 'Not rated yet' }}
          </div>
          </td>
          <td class="border-t">
              <div>
                <span class="font-bold py-2">{{ taskFile.tanggal_kumpul > task.due_date ? 'LATE' : '' }}</span>
              </div>
              {{ taskFile.tanggal_kumpul }}
          </td>
          <td class="border-t">
            <div class="flex">
              <button class="btn-indigo mr-2" data-toggle="tooltip" title="Rate All" @click="toggleModal('Task', taskFile.id)">
                <i class="fa fa-check" aria-hidden="true" />
              </button>
            </div>
          </td>
        </tr>
        <tr v-if="taskFiles.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No file upload</td>
        </tr>
      </table>
    </div>
    <pagination :links="taskFiles.links" />

    <div v-if="modal.showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
      <div class="relative w-auto my-6 mx-auto max-w-6xl">
        <!--content-->
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between p-5 border-b border-solid border-gray-300 rounded-t">
            <h3 class="text-3xl font-semibold">
              {{ modal.titleModal }}
            </h3>
            <button class="p-1 ml-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none" @click="closeModal">
              <span class="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
                ×
              </span>
            </button>
          </div>
          <!--body-->
          <div class="relative p-6 flex-auto">
            <input v-model="form.task_file_id" type="hidden">
            <input v-model="form.task_id" type="hidden">
            <text-input
              v-model="form.mark"
              type="number"
              :errors="$page.errors.mark"
              class="pr-6 pb-8 w-full lg"
              label="Mark"
            />
          </div>
          <!--footer-->
          <div class="flex items-center justify-end p-6 border-t border-solid border-gray-300 rounded-b">
            <button class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease" @click="closeModal">
              Cancel
            </button>
            <button class="text-blue-500 bg-transparent border border-solid border-blue-500 hover:bg-blue-500 hover:text-white active:bg-blue-600 font-bold uppercase text-sm px-6 py-3 rounded outline-none focus:outline-none mr-1 mb-1" type="submit" style="transition: all .15s ease" @click="submit">
              Submit
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="modal.showModal" class="opacity-25 fixed inset-0 z-40 bg-black" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import TextInput from '@/Shared/TextInput'

export default {
  metaInfo: { title: 'All Student Upload' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
    TextInput,
  },
  props: {
    task: Object,
    taskFiles: Object,
    filters: Object,
  },
  data() {
    return {
      modal: {
        showModal: false,
        // descModal: 'Apakah anda yakin ingin keluar dari Course ini?',
        // titleModal: "Keluar Course",
        // course_id: null,
      },
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form)
        this.$inertia.replace(
          this.route(
            'tasks.get_student_upload', this.task.id,
            Object.keys(query).length ? query : { remember: 'forget' }
          )
        )
      }, 150),
      deep: true,
    },
  },
  methods: {
    toggleModal(title, task_file_id = null, task_id = null) {
      this.modal.showModal = !this.modal.showModal
      this.modal.titleModal = title
      this.form.task_file_id = task_file_id
      this.form.task_id = task_id
    },
    closeModal() {
      this.modal.showModal = false
      this.form.task_file_id = null
      this.form.mark = null
    },
    submit() {
      var data = new FormData()
      data.append('mark', this.form.mark || '')
      data.append('task_file_id', this.form.task_file_id || '')
      data.append('task_id', this.form.task_id || '')
      this.$inertia.post(this.route('tasks.mark_student_task'), data)
        .then(() => {
          this.form.mark = null
          this.form.task_file_id = null
          this.form.task_id = null
          this.modal.showModal = false
        })
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
