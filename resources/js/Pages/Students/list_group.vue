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
      <inertia-link class="btn-indigo" :href="route('student-groups')">
        <span>My Group</span>
      </inertia-link>
    </div>
    <div class="mb-6 flex flex-wrap items-center">
      <text-input v-model="form.tahun" :errors="$page.errors.tahun" class="pr-6 pb-8 w-full lg:w-1/4" label="Year" />
      <select-input v-model="form.academicterms" :errors="$page.errors.academicterms" class="pr-6 pb-8 w-full lg:w-1/4" label="Academicterms">
        <option :value="null" />
        <option value="1">Odd</option>
        <option value="2">Even</option>
      </select-input>
      <!-- <text-input v-model="form.academicterms" :errors="$page.errors.academicterms" class="pr-6 pb-8 w-full lg:w-1/4" label="Semester" /> -->
      <span class="lg:w-1/2"></span>
      <select-input v-model="form.mainteacher" :errors="$page.errors.mainteacher" class="pr-6 pb-8 w-full lg:w-1/4" label="Main Teacher">
        <option :value="null" />
        <option v-for="user in listTeachers" :key="user.id"  :value="user.id">{{user.name}}</option>
      </select-input>
      <!-- <text-input v-model="form.mainteacher" :errors="$page.errors.mainteacher" class="pr-6 pb-8 w-full lg:w-1/4" label="Guru" /> -->
      <text-input v-model="form.classes" :errors="$page.errors.classes" class="pr-6 pb-8 w-full lg:w-1/4" label="Class" />
    
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">ID</th>
          <th class="px-6 pt-6 pb-4">Classes</th>
          <th class="px-6 pt-6 pb-4">Year</th>
          <th class="px-6 pt-6 pb-4">Gender</th>
          <th class="px-6 pt-6 pb-4">Academicterms</th>
          <th class="px-6 pt-6 pb-4">Main Teacher</th>
          <th class="px-6 pt-6 pb-4">Total Student</th>
          <th class="px-6 pt-6 pb-4">Action</th>
          <!-- <th class="px-6 pt-6 pb-4" colspan="2">Phone</th> -->
        </tr>
        <tr v-for="group in groups.data" :key="group.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('group_users.show', group.id)">
              {{ group.id }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('group_users.show', group.id)">
              {{ group.classes+"-"+group.huruf }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.year }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.kel_kelas }}
            </span>
          </td>
          <!-- <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.code }}
            </inertia-link>
          </td> -->
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.academicterms === 1 ? 'Ganjil' : "Genap" }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              <div v-if="group.mainteacherr">
                {{ group.mainteacherr.fullname }}
              </div>
              <div v-else>
                Empty
              </div>
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.total_peserta }}
            </span>
          </td>
          <td class="border-t">
            <span class="btn-indigo p-2 cursor-pointer" v-on:click="toggleModal('Kelas : '+ group.classes + ', tahun : '+ group.year + ' Semester : ' + (group.academicterms === 1 ? 'Ganjil' : 'Genap'), group.id )">
              Join
            </span>
          </td>
        </tr>
        <tr v-if="groups.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No organizations found.</td>
        </tr>
      </table>
    </div>
    <pagination :links="groups.links" />
    

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
              Join Group
            </h3>
            <button class="p-1 ml-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none" v-on:click="toggleModal()">
              <span class="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
                ×
              </span>
            </button>
          </div>
          <!--body-->
          <flash-messages class="w-full m-2"/>
          <form @submit.prevent="submit">
          <div class="relative p-6 flex-auto">
            <p class="mb-4 text-gray-600 text-lg leading-relaxed">
              You want to join {{this.descModal}}, before that please enter the group code first
            </p>
            <div class="flex items-center justify-center">
            <text-input v-model="modalForm.code" :errors="$page.errors.code" class="pr-6 pb-8 w-full lg:w-1/2" label="Group Code" />

            </div>
          </div>
          <!--footer-->
          <div class="flex items-center justify-end p-6 border-t border-solid border-gray-300 rounded-b">
            <button class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease" v-on:click="toggleModal()">
              Cancel
            </button>
            <button class="text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-sm px-6 py-3 rounded outline-none focus:outline-none mr-1 mb-1" type="submit" style="transition: all .15s ease">
              Join Group
            </button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>

    <!-- <pdf-viewer :pdfurl='pdf.ur l' :pdfTitle="pdf.title" > </pdf-viewer> -->
    
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
import PdfViewer from '@/Shared/PdfViewer'
import FlashMessages from '@/Shared/FlashMessages'

export default {
  metaInfo: { title: 'Groups' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    TextInput,
    SearchFilter,
    SelectInput,
    PdfViewer,
    FlashMessages,
  },
  props: {
    groups: Object,
    filters: Object,
    menuName: String,
    listTeachers: Array,
  },
  data() {
    return {
      showModal: false,
      descModal: null,
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        tahun: this.filters.tahun,
        academicterms: this.filters.academicterms,
        mainteacher: this.filters.mainteacher,
        classes: this.filters.classes,
      },
      modalForm: {
        group_id :null,
        code: null,
      },
      pdf:{
        url: "https://documentcloud.adobe.com/view-sdk-demo/PDFs/Bodea Brochure.pdf",
        title: "Bodea Brochure.pdf",
      }
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('groups-users-student', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    toggleModal: function(info = '', group_id = null){
      this.showModal = !this.showModal;
      this.descModal = info;
      this.modalForm.group_id = group_id;
      // alert(info);
    },
    submit() {
      // this.sending = true
      this.$inertia.post(this.route('students.join-group'), this.modalForm)
        .then(() => 
        {
          this.showModal = (this.$page.flash.error || this.$page.errors.code) && !this.$page.flash.success 
        })
    },
  },
}
</script>
