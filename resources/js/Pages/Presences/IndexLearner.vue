<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('detail.course.modules', dataCourseModule.id)">Materi</inertia-link>
        <span class="text-indigo-400 font-medium"> / </span>Student Attendance Status Data 
        </h1>
    <h1 class="mb-8 font-bold text-xl">Materi : {{dataCourseModule.title}}</h1>
    <h1 class="mb-8 font-bold text-l">
        Presence Start Time : {{dataCourseModule.actual_start_at_indonesia}} 
        <span v-if="dataCourseModule.actual_end_at" > s/d {{dataCourseModule.actual_end_at_indonesia}}</span>

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
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Presence Status</th>
          <th class="px-6 pt-6 pb-4">Information</th>
          <th class="px-6 pt-6 pb-4">Attendance Time</th>
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
          
          
        </tr>
        <tr v-if="dataPresences.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">Presence data does not exist</td>
        </tr>
      </table>
    </div>
    <pagination :links="dataPresences.links" />



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
  
  },
}
</script>



