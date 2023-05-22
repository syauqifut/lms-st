<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl"><inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('groups')">Group / </inertia-link>{{ menuName }}</h1>
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
          <th class="px-6 pt-6 pb-4">Students</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr v-for="group in groups" :key="group.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ group.group.classes + " - " + group.group.huruf +" " + group.group.kel_kelas+ " " + (group.group.academicterms === 1 ? 'Odd' : "Even") + " " + group.group.year }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ group.user.fullname }}
            </span>
          </td>
          <td class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              <span class="btn-indigo p-2 cursor-pointer mr-2" @click="approve(group.user.fullname, group.group.classes,group.group.huruf,group.group.kel_kelas, group.id)">
                Approve
              </span>
              <button class="mt-2 text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-sm p-2 rounded outline-none focus:outline-none mr-1 mb-1" type="submit" style="transition: all .15s ease" @click="decline(group.user.fullname, group.group.classes, group.group.huruf,group.group.kel_kelas, group.id)">
                Refuse
              </button>
            </span>
          </td>
        </tr>
        <tr v-if="groups.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No approve request found.</td>
        </tr>
      </table>
    </div>
    <pagination :links="groups.links" />
    <modal-confirmation :show-modal="modal.showModal" :title-modal="modal.titleModal" :desc-modal="modal.descModal" @toogle="toggleModal" @confirm="confirmModal" />
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
  metaInfo: { title: 'Groups' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
    ModalConfirmation,
  },
  props: {
    groups: Array,
    showModal: false,
    filters: Object,
    menuName: String,
  },
  data() {
    return {
      modal: {
        showModal: false,
        descModal: 'Are you sure want to leave this group?',
        titleModal: 'Leave Group',
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
        this.$inertia.replace(this.route('group_users', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    approve:function(murid, group,huruf,kel_kelas, group_user_id){
      this.modal.descModal = 'Approve '+ murid + ' to group ' + group + '-' + huruf + ' ' + kel_kelas + '?'
      this.modal.titleModal = 'Approve Student?'
      this.modal.url = route('approve-group-student', group_user_id)
      this.toggleModal()
    },
    decline:function(murid, group, huruf,kel_kelas, group_user_id){
      this.modal.descModal = 'Refuse '+ murid + ' to group ' + group + '-' + huruf + ' ' + kel_kelas + '?'
      this.modal.titleModal = 'Refuse Student?'
      this.modal.url = route('decline-group-student', group_user_id)
      this.toggleModal()
    },
    toggleModal: function(){
      this.modal.showModal = !this.modal.showModal
    },
    confirmModal: function(){
      this.$inertia.replace(this.modal.url)
        .then(() => this.modal.showModal = false)
    },
  },
}
</script>
