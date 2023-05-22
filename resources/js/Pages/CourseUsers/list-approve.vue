<template>
  <div>
    

    <h1 class="mb-8 font-bold text-3xl"><inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('courses.index')">Learning / </inertia-link>{{menuName}}</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <!-- <th class="px-6 pt-6 pb-4">ID</th> -->
          <th class="px-6 pt-6 pb-4">Group</th>
          <th class="px-6 pt-6 pb-4">Learning</th>
          <th class="px-6 pt-6 pb-4">Student</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr v-for="course in courses" :key="course.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ course.course.group.classes + " - " + (course.course.group.academicterms === 1 ? 'Ganjil' : "Genap") + " " + course.course.group.year}}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ course.course.title}}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ course.user.fullname}}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              <span class="btn-indigo p-2 cursor-pointer mr-2" v-on:click="approve(course.user.fullname, course.course.title, course.id)">
                Approve
              </span>
              <button v-on:click="decline(course.user.fullname, course.course.title, course.id)" class="mt-2 text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-sm p-2 rounded outline-none focus:outline-none mr-1 mb-1" type="submit" style="transition: all .15s ease">
                Rejected
              </button>
            </span>
          </td>
          
        </tr>
        <tr v-if="courses.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No organizations found.</td>
        </tr>
      </table>
    </div>
    <pagination :links="courses.links" />
    <modal-confirmation :showModal="modal.showModal" :titleModal="modal.titleModal" :descModal="modal.descModal" v-on:toogle="toggleModal" v-on:confirm="confirmModal"> </modal-confirmation>
    
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
import ModalConfirmation from '@/Shared/ModalConfirmation'

export default {
  metaInfo: { title: 'Learning' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
    ModalConfirmation,
  },
  props: {
    courses: Array,
    showModal: false,
    filters: Object,
    menuName: String,
  },
  data() {
    return {
      modal: {
        showModal: false,
        descModal: 'Are you sure you want to leave this group?',
        titleModal: "Leave Group",
        url: null,
      },
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('course_users', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    approve:function(murid, course, course_user_id){
      this.modal.descModal = "Approve "+ murid + " to learning " + course + "?"
      this.modal.titleModal = "Approve Student?"
      this.modal.url = route('approve-course-student', course_user_id)
      this.toggleModal()
    },
    decline:function(murid, course, course_user_id){
      this.modal.descModal = "Reject "+ murid + " to learning " + course + "?"
      this.modal.titleModal = "Reject Student?"
      this.modal.url = route('decline-course-student', course_user_id)
      this.toggleModal()
    },
    toggleModal: function(){
      this.modal.showModal = !this.modal.showModal;
    },
    confirmModal: function(){
      this.$inertia.replace(this.modal.url)
      .then(() => this.modal.showModal = false)
    }
  },
}
</script>
