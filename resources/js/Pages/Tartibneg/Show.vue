<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('group_users.show', group.id)">{{ group.classes }}</inertia-link> /
      List of Violations
    </h1>

    <div v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4" class="mb-8">
      <inertia-link class="btn-indigo" :href="route('tartibneg_users.create', groupUser.id)">Add Violation</inertia-link>
    </div>

    <!-- component -->

    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10 " x-data="{welcomeMessageShow:true}" x-show="welcomeMessageShow" x-transition:enter="transition-all ease duration-500 transform" x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition-all ease duration-500 transform" x-transition:leave-end="opacity-0 scale-90">
      <div class="w-full flex flex-wrap">
        <div class="w-full sm:w-1/2 md:w-2/4 px-3 text-left">
          <div class="p-5 xl:px-8 md:py-5">
            <h3 class="text-2xl mb-3">{{ user.fullname }}</h3>
            <h5 class="text-xl mb-3">{{ group.classes }}</h5>
            <!-- <h5 class="text-xl mb-3">Total : {{ sumpoin }} poin</h5> -->
          </div>
        </div>

        <div class="w-full sm:w-1/2 md:w-2/4 px-3 text-left">
          <div v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4">
            <form @submit.prevent="submit" ref="form">
            <!-- <div class="pr-6 pb-8 w-full lg:w-1/1">
              <h1 class="mb-1">Pilih User</h1>
              <v-select label="fullname" :reduce="user_for_select_option => user_for_select_option.id" v-model="form.user_id" :options="user_for_select_option"></v-select>
            </div> -->
            </form>
          </div>
          <div v-else class="flex justify-end">
            <span class="bg-transparent cursor-pointer hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded"
                  v-on:click="toggleModal()">Leave</span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white font-bold rounded shadow-xl py-5 px-5 w-full mb-10">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Date</th>
          <th class="px-6 pt-6 pb-4">Reporter</th>
          <th class="px-6 pt-6 pb-4">Code</th>
          <th class="px-6 pt-6 pb-4">Name Violation</th>
           <th class="px-6 pt-6 pb-4">Notes</th>
          <th class="px-6 pt-6 pb-4">Point</th>
        </tr>
        <tr
          v-for="tartibneg in tartibuser"
          :key="tartibneg.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibneg.date }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibneg.fullname }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibneg.kode_pelanggaran }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibneg.nama_pelanggaran }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibneg.catatan }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibneg.poin }}
            </div>
          </td>
        </tr>
        <tr v-if="tartibuser.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="7">No violation data.</td>
        </tr>
      </table>
    </div>
    <modal-confirmation :showModal="modal.showModal" :titleModal="modal.titleModal" :descModal="modal.descModal" v-on:toogle="toggleModal" v-on:confirm="confirmModal"> </modal-confirmation>

  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import ModalConfirmation from '@/Shared/ModalConfirmation'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    ModalConfirmation,
  },

  props: {
    user: Object,
    group: Object,
    groupUser: Object,
    sumpoin: Number,
    tartibuser: Array,
  },
  remember: 'form',
  data() {
    return {
      status:{
        '-2' : 'Leave',
        '-1' : 'Rejected',
        '0' : 'Pending',
        '1' : 'Active',
      },
      modal: {
        showModal: false,
        descModal: 'Are you sure you want to leave this group?',
        titleModal: "Leave Group",
      },
      sending: false,
      forGroup: {
        name: this.group.classes,
        classes: this.group.classes,
        mainteacher: this.group.mainteacher,
        academicterms: this.group.academicterms,
        year: this.group.year,
      },
      form: {
        user_id : null,
        group_id: this.group.id,
      },
    }
  },
  methods: {
    submit() {

      this.sending = true
      var data = new FormData()

      data.append('user_id', this.form.user_id || '')
      data.append('group_id', this.form.group_id || '')

      this.$inertia.post(this.route('group_users.store'), data)
        .then(() => this.sending = false)

    },
    destroy(id,fullname) {
      if (confirm('Are you sure to delete data  '+fullname+'?')) {
        this.$inertia.delete(this.route('group_users.destroy', id))
      }
    },
    toggleModal: function(){
      this.modal.showModal = !this.modal.showModal;
    },
    confirmModal: function(){
      this.$inertia.replace(route('student-groups-exit', this.form.group_id))
      .then(() => this.modal.showModal = false)
    }
  },
}
</script>
