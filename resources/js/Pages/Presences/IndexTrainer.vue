<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('detail.course.modules', dataCourseModule.id)">Materi</inertia-link>
        <span class="text-indigo-400 font-medium"> / </span>Student Attendance Status Data
        </h1>
    <h1 class="mb-8 font-bold text-xl">Materi : {{dataCourseModule.title}}</h1>


    
    <div class="mb-6 flex justify-between ">
      <h1 class="mb-8 font-bold text-l">
          Presence Start Time : {{dataCourseModule.actual_start_at_indonesia}} 
          <span v-if="dataCourseModule.actual_end_at" > s/d {{dataCourseModule.actual_end_at_indonesia}}</span>
      </h1>

      <inertia-link  v-if="!dataCourseModule.actual_end_at" class="btn-indigo" :href="route('presences.stop', dataCourseModule.id)">
        <span>End of Presence</span>
      </inertia-link>

    </div>



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
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Presence Status</th>
          <th class="px-6 pt-6 pb-4">Description</th>
          <th class="px-6 pt-6 pb-4">Attendance Time</th>
          <th class="px-6 pt-6 pb-4">Presence Status</th>
        </tr>
        <tr v-for="dataPresent in dataPresences.data" :key="dataPresent.student_user_id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        
          <td class="border-t">              
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" href="#">
              {{ dataPresent.fullname }} 
            </inertia-link> 
          </td> 
          
          <td class="border-t">              
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" href="#">
              {{ dataPresent.status }} 
            </inertia-link> 
          </td> 
          
          <td class="border-t">              
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" href="#">
              {{ dataPresent.description }} 
            </inertia-link> 
          </td> 
          <td class="border-t">
              
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" href="#">
              {{ dataPresent.date_complete_indonesia }} 
            </inertia-link> 
          </td> 
          
          <td class="border-t">
            <div v-if="$page.auth.user.usertype_id =='1' || $page.auth.user.usertype_id =='4'">
                <span class="inline-block bg-green-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="hadirToggleModal(dataPresent.student_user_id)" >
                  Hadir 
                </span>
                <span class="inline-block bg-yellow-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="sakitToggleModal(dataPresent.student_user_id)" >
                  Sakit 
                </span>
                <span class="inline-block bg-orange-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="izinToggleModal(dataPresent.student_user_id)" >
                  Izin 
                </span>
                <span class="inline-block bg-red-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="alphaToggleModal(dataPresent.student_user_id)" >
                  Alpha 
                </span>
                <span v-if="dataPresent.usertype_id =='3'" class="inline-block bg-blue-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="nonToggleModal(dataPresent.student_user_id)" >
                  NoN
                </span>
            </div>
            <div v-else>
              <div v-if="dataPresent.usertype_id =='2' ">
                 <span class="inline-block bg-green-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="hadirToggleModal(dataPresent.student_user_id)" >
                  Hadir 
                </span>
                <span class="inline-block bg-yellow-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="sakitToggleModal(dataPresent.student_user_id)" >
                  Sakit 
                </span>
                <span class="inline-block bg-orange-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="izinToggleModal(dataPresent.student_user_id)" >
                  Izin 
                </span>
                <span class="inline-block bg-red-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" v-on:click="alphaToggleModal(dataPresent.student_user_id)" >
                  Alpha 
                </span>
              </div>
            </div>
          </td>

        </tr>
        <tr v-if="dataPresences.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">Presence data does not exist</td>
        </tr>
      </table>
    </div>
    <pagination :links="dataPresences.links" />



   <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
      <div class="relative w-auto my-6 mx-auto max-w-6xl">
        <!--content-->
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between p-5 border-b border-solid border-gray-300 rounded-t">
            <h3 class="text-xl font-semibold">
              Student Attendance Status Information Form
            </h3>
            <button class="p-1 ml-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none" v-on:click="closeModal()">
              <span class="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
                ×
              </span>
            </button>
          </div>
          <!--body-->
          <form @submit.prevent="submit">
          <div class="relative p-6 flex-auto">
             <div class="flex items-center justify-center">
                 <text-input v-model="modalForm.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full " label="Description" />
                
            </div>
          </div>
          <!--footer-->
          <div class="flex items-center justify-end p-6 border-t border-solid border-gray-300 rounded-b">
           <button class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease" v-on:click="closeModal()">
              Cancel
            </button>
            <button class="text-green-500 bg-transparent border border-solid border-green-500 hover:bg-green-500 hover:text-white active:bg-green-600 font-bold uppercase text-sm px-6 py-3 rounded outline-none focus:outline-none mr-1 mb-1" type="submit" style="transition: all .15s ease">
              Save
            </button>
          </div>
          </form>
        </div>
      </div>
    </div>
    <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>




  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Presence Data' },
  layout: Layout,
  components: {
    Icon,
    TextInput,
    Pagination,
    SearchFilter,
    SelectInput,
  },
  props: {
    dataPresences: Object,
    dataCourseModule: Object,
    filters: Object,
  },
  data() {
    return {
      showModal: false,
      form: {
        course_module_id: this.dataCourseModule.id,
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
      modalForm: {
        student_user_id   :   null,
        coursemodule_id   :   this.dataCourseModule.id,
        status            :   null,
        description       :   null,
      },
     
    }
  },
  watch: {
    form: {
      handler: throttle(function() {

        let query = pickBy(this.form)
        this.$inertia.replace(this.route('presences-show', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    hadirToggleModal: function(student_user_id){

      this.showModal = !this.showModal;

      this.modalForm.student_user_id  = student_user_id;
      this.modalForm.status  = "P";
      
    },
    sakitToggleModal: function(student_user_id){

      this.showModal = !this.showModal;

      this.modalForm.student_user_id  = student_user_id;
      this.modalForm.status  = "S";
      
    },
    izinToggleModal: function(student_user_id){

      this.showModal = !this.showModal;

      this.modalForm.student_user_id  = student_user_id;
      this.modalForm.status  = "L";
      
    },
    alphaToggleModal: function(student_user_id){

      this.showModal = !this.showModal;

      this.modalForm.student_user_id  = student_user_id;
      this.modalForm.status  = "A";
      
    },
    nonToggleModal: function(student_user_id){

      this.showModal = !this.showModal;

      this.modalForm.student_user_id  = student_user_id;
      this.modalForm.status  = "N";
      
    },
    closeModal(){

      this.showModal = false;

    },
    submit() {
      // this.sending = true
      this.$inertia.post(this.route('presences.update-status'), this.modalForm)
        .then(() => 
        {
          this.sending = false
          this.modalForm.student_user_id = null
          this.modalForm.description = null
          this.modalForm.status = null
          this.showModal = (this.$page.flash.error || this.$page.errors.code) && !this.$page.flash.success 
        })
    },
  
  },
}
</script>



