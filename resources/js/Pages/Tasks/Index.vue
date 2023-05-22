<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('detail.course.modules', courseModule.id)">{{ courseModule.title }}</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Tasks
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
      <inertia-link v-if="$page.auth.user.usertype_id != 2 " class="btn-indigo" :href="route('tasks.create', [courseModule.id])">
        <span>Create</span>
        <span class="hidden md:inline">Task</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
     <table class="w-full no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Class</th>
          <th class="px-6 pt-6 pb-4">Date</th>
          <th class="px-6 pt-6 pb-4">Type</th>
          <th class="px-6 pt-6 pb-4">Collection Limit</th>
          <th class="px-6 pt-6 pb-4">File</th>
          <th class="px-6 pt-6 pb-4">Short Task</th>
          <th class="px-6 pt-6 pb-4">Link</th>
          <!-- <th class="px-6 pt-6 pb-4">Penilaian Otomatis</th>
          <th class="px-6 pt-6 pb-4">Random Order</th> -->
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr
          v-for="task in tasks.data"
          :key="task.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ task.name }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ task.group.classes }}{{ task.group.huruf }} - {{ task.group.year }} ({{ task.group.kel_kelas }})
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ task.date }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ task.task_type }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ task.due_date }}
            </div>
          </td>
          <td class="border-t">
            <a v-if="task.file" :href="route('tasks.preview', task.id)" class="text-blue-500 text-lg hover:underline" target="_blank">{{ task.file.substring(11,29)}}<span v-if="task.file.length>=29">...</span></a>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ task.soal }}
            </div>
          </td>

          <td class="border-t">
            <span v-if="task.link" class="text-blue-500 text-lg hover:underline" @click="openLink(task.link)">{{ task.link.substring(0,20) }}<span v-if="task.link.length>=20">...</span></span>
            <!-- <router-link :to="{ name: task.link}" target="_blank">
              {{ task.link }}
            </router-link> -->
          </td>
          <!-- <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ task.auto_mark ? 'Otomatis' : 'Manual' }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ task.random_order ? 'Acak' : 'Urut' }}
            </div>
          </td> -->

          <td class="border-t ">
            <!-- TOMBOL AKSI ROLE BUKAN MURID -->
            <div v-if="!role.is_murid" class="flex">
              <inertia-link data-toggle="tooltip" title="Modules" class="btn-indigo mr-2 " :href="route('tasks.edit', task.id)" tabindex="-1">
                <i class="fa fa-pencil" aria-hidden="true" />
              </inertia-link>
              <inertia-link class="btn-indigo mr-2 " :href="route('tasks.get_student_upload', task.id)" tabindex="-1">
                <i class="fa fa-book" aria-hidden="true" />
              </inertia-link>
            </div>

            <!-- TOMBOL AKSI ROLE MURID -->
            <div v-else class="flex">
              <inertia-link v-if="task.file" data-toggle="tooltip" title="Modules" :class="task.user_upload ? ' btn-indigo mr-2' : 'btn-bland mr-2'" :href="route('tasks.student_upload_form', task.id)" tabindex="-1">
                <i class="fa fa-upload" aria-hidden="true" />
              </inertia-link>

              <inertia-link v-if="task.soal" data-toggle="tooltip" title="Modules" :class="task.user_upload ? ' btn-indigo mr-2' : 'btn-bland mr-2'" :href="route('tasks.student_upload_form', task.id)" tabindex="-1">
                <i class="fa fa-upload" aria-hidden="true" />
              </inertia-link>

              <inertia-link v-else-if="task.link && !task.user_upload" data-toggle="tooltip" title="Modules" class="btn-bland mr-2" :href="route('tasks.finish_assignment', task.id)" tabindex="-1">
                <i class="fa fa-check" aria-hidden="true" />
              </inertia-link>

              <inertia-link v-else-if="task.link && task.user_upload" data-toggle="tooltip" title="Modules" class="btn-indigo mr-2" :href="route('tasks.student_mark_task_link', task.id)" tabindex="-1">
                View Mark
              </inertia-link>
            </div>
            <!--// TOMBOL AKSI ROLE MURID //-->
          </td>
        </tr>
        <tr v-if="tasks.data.length === 0">
          <td class="border-t px-6 py-4" colspan="8">No tasks</td>
        </tr>
      </table>
    </div>
    <pagination :links="tasks.links" />
    <!-- <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSf-f_6Dp6v7F5SAIrrpXHmt8ZQf_BGp2TawSMVi3ncqPt81Cw/viewform?embedded=true" width="700" height="520" frameborder="0" marginheight="0" marginwidth="0">Loading…</iframe> -->
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

export default {
  metaInfo: { title: 'Tasks' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    courseModule: Object,
    tasks: Object,
    filters: Object,
  },
  data() {
    return {
      role: {
        is_murid: this.$page.auth.user.usertype_id == 2,
        // is_teacher: this.$page.auth.user.usertype_id == 3 || this.$page.auth.user.usertype_id == 1,
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
            'tasks.index', this.courseModule.id,
            Object.keys(query).length ? query : { remember: 'forget' }
          )
        )
      }, 150),
      deep: true,
    },
  },
  methods: {
    openLink(link) {
      alert(link)
      window.open(link, '_blank')
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
