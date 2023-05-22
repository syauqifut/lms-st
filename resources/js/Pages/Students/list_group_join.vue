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
      <inertia-link class="btn-indigo" :href="route('groups')">
        <span>Group List</span>
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
          <th class="px-6 pt-6 pb-4">Status</th>
         
          <!-- <th class="px-6 pt-6 pb-4" colspan="2">Phone</th> -->
        </tr>
        <tr v-for="group in groups.data" :key="group.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td v-if="group.status==1" class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('group_users.show', group.id)">
              {{ group.id }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td v-else class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ group.id }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>

          <td v-if="group.status==1" class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('group_users.show', group.id)">
              {{ group.classes+"-"+group.huruf }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td v-else class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
               {{ group.classes+"-"+group.huruf }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>
          
          <td v-if="group.status==1" class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.year }}
            </inertia-link>
          </td>
          <td v-else class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ group.year }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>
          <td v-if="group.status==1" class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.kel_kelas }}
            </inertia-link>
          </td>
          <td v-else class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ group.kel_kelas }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>

          <td v-if="group.status==1" class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.academicterms === 1 ? 'Ganjil' : "Genap" }}
            </inertia-link>
          </td>
          <td v-else class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
               {{ group.academicterms === 1 ? 'Ganjil' : "Genap" }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>

          <td v-if="group.status==1" class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              <div v-if="group.mainteacherr">
                {{ group.mainteacherr.fullname }}
              </div>
              <div v-else>
                Empty
              </div>
            </inertia-link>
          </td>
           <td v-else class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
             <div v-if="group.mainteacherr">
                {{ group.mainteacherr.fullname }}
              </div>
              <div v-else>
                Empty
              </div>
            </span>
          </td>

          <td v-if="group.status==1" class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.total_peserta }}
            </inertia-link>
          </td>
            <td v-else class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
               {{ group.total_peserta }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>

          <td v-if="group.status==1" class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group_user_status[group.status]}}
            </inertia-link>
          </td>
            <td v-else class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
               {{ group_user_status[group.status]}}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>

         
        </tr>
        
        <tr v-if="groups.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No Groups found.</td>
        </tr>
      </table>
    </div>
    <pagination :links="groups.links" />
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
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import ModalConfirmation from '@/Shared/ModalConfirmation'

export default {
  metaInfo: { title: 'Groups' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    TextInput,
    SelectInput,
    SearchFilter,
    ModalConfirmation,
  },
  props: {
    groups: Object,
    filters: Object,
    menuName: String,
    listTeachers: Array,
  },
  data() {
    return {
      modal: {
        showModal: false,
        descModal: 'Are you sure you want to leave this group??',
        titleModal: "Leave the Group",
        group_id: null,
      },
      
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        tahun: this.filters.tahun,
        academicterms: this.filters.academicterms,
        mainteacher: this.filters.mainteacher,
        classes: this.filters.classes,
        
      },
      group_user_status:{
        '-2' : "Keluar",
        '-1' : "Ditolak",
        '0'  : "Approval",
        '1'  : "Aktif",
      },
      modalForm: {
        group_id :null,
        code: null,
      }
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('student-groups', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    toggleModal: function(group_id = null){
      this.modal.showModal = !this.modal.showModal;
      this.modal.group_id= group_id;
    },
    confirmModal: function(){
      this.$inertia.replace(route('student-groups-exit', this.modal.group_id))
      .then(() => this.modal.showModal = false)
    }
  },
}
</script>
