<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">{{menuName}}</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('courses.index')">
        <span>My Learning</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
           <th class="px-6 pt-6 pb-4">Ttiel</th>
          <th class="px-6 pt-6 pb-4">Subject</th>
          <th class="px-6 pt-6 pb-4">Category</th>
          <th class="px-6 pt-6 pb-4">Level</th>
          <th class="px-6 pt-6 pb-4">Teacher</th>
          <th class="px-6 pt-6 pb-4">Group</th>
          <th class="px-6 pt-6 pb-4">Status</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ course.title }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ course.subject ? course.subject.name : '' }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center"  tabindex="-1">
              {{ course.category ? course.category.title : '' }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" tabindex="-1">
              {{ course.level ? course.level.title : '' }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" tabindex="-1">
                {{ course.teacher ? course.teacher.fullname : '' }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" tabindex="-1">
              {{ course.group ? course.group.classes : '' }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" tabindex="-1">
              {{ course.is_active ? 'Aktif' : 'Tidak Aktif' }}
            </span>
          </td>
          <td class="border-t">
            <span class="btn-indigo p-2 cursor-pointer" v-on:click="toggleModal('Course : '+ course.title + ', Group: ' + (course.group ? course.group.classes : ''), course.id )">
              Join
            </span>
          </td>
        </tr>
        <tr v-if="courses.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No organizations found.</td>
        </tr>
      </table>
    </div>
    <pagination :links="courses.links" />
    

<!-- 
    <button class="bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-sm px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease" v-on:click="toggleModal()">
      Open large modal
    </button> -->
    <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
      <div class="relative w-auto my-6 mx-auto max-w-6xl">
        <!--content-->
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between p-5 border-b border-solid border-gray-300 rounded-t">
            <h3 class="text-3xl font-semibold">
              Join Learning
            </h3>
            <button class="p-1 ml-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none" v-on:click="toggleModal()">
              <span class="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
                ×
              </span>
            </button>
          </div>

          <flash-messages class="w-full m-2"/>
          <!--body-->
          <form @submit.prevent="submit">
          <div class="relative p-6 flex-auto">
            <p class="mb-4 text-gray-600 text-lg leading-relaxed">
              You want to join {{this.descModal}}, before that please enter the learning code first
            </p>
            <div class="flex items-center justify-center">
            <text-input v-model="modalForm.code" :errors="$page.errors.code" class="pr-6 pb-8 w-full lg:w-1/2" label="Kode Course" />

            </div>
          </div>
          <!--footer-->
          <div class="flex items-center justify-end p-6 border-t border-solid border-gray-300 rounded-b">
            <button class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease" v-on:click="toggleModal()">
              Cancel
            </button>
            <button class="text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-sm px-6 py-3 rounded outline-none focus:outline-none mr-1 mb-1" type="submit" style="transition: all .15s ease">
              Join Learning
            </button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>

    <!-- <pdf-viewer :pdfurl='pdf.url' :pdfTitle="pdf.title" > </pdf-viewer> -->
    
  </div>

  
</template>


<script type="text/javascript">
  
  
</script>
<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
// import PdfViewer from '@/Shared/PdfViewer'
import FlashMessages from '@/Shared/FlashMessages'

export default {
  metaInfo: { title: 'Courses' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    TextInput,
    SearchFilter,
    SelectInput,
    FlashMessages,
    // PdfViewer,
  },
  props: {
    courses: Object,
    filters: Object,
    menuName: "List Courses Student",
  },
  data() {
    return {
      showModal: false,
      descModal: null,
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        // tahun: this.filters.tahun,
        // academicterms: this.filters.academicterms,
        // mainteacher: this.filters.mainteacher,
        // classes: this.filters.classes,
      },
      modalForm: {
        course_id :null,
        code: null,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('course-users-student', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    toggleModal: function(info = '', course_id = null){
      this.showModal = !this.showModal;
      this.descModal = info;
      this.modalForm.course_id = course_id;
      // alert(info);
    },
    submit() {
      // this.sending = true
      this.$inertia.post(this.route('students_join_course'), this.modalForm)
        .then(() => 
        {
          this.showModal = (this.$page.flash.error || this.$page.errors.code) && !this.$page.flash.success 
        })
    },
  },
}
</script>
