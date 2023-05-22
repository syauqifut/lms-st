<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link v-if="$page.auth.user.usertype_id == 2" class="text-indigo-400 hover:text-indigo-600" :href="route('student-groups')">Group</inertia-link>
      <inertia-link v-else class="text-indigo-400 hover:text-indigo-600" :href="route('groups')">Group</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ forGroup.name }} 
      {{ '  ' + group.huruf }}
      <span class="text-xl">{{ ' - ' + group.code }}</span>
      
    </h1>


    <div v-if="showDownloadExcel" class="mb-6 flex justify-between items-center">
      <a class="btn-indigo" :href="route('group-users.export', group.id)">
        <span>Export Excel</span>
      </a>
    </div>
    <div v-else></div>
    <!-- component -->

    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10 " x-data="{welcomeMessageShow:true}" x-show="welcomeMessageShow" x-transition:enter="transition-all ease duration-500 transform" x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition-all ease duration-500 transform" x-transition:leave-end="opacity-0 scale-90">
      <div class="w-full flex flex-wrap">
        <div class="w-full sm:w-1/2 md:w-2/4 px-3 text-left">
          <div class="p-5 xl:px-8 md:py-5">
            <h3 class="text-2xl mb-3">{{ forGroup.classes }} {{ '  ' + group.huruf }}</h3>
            <h5 class="text-xl mb-3">{{ forGroup.year }}</h5>
            <h5 class="text-xl mb-3">{{ teacher.fullname }}</h5>
          </div>
        </div>

        <div class="w-full sm:w-1/2 md:w-2/4 px-3 text-left">
          <div v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 4">
            <form ref="form" @submit.prevent="submit">
              <div class="pr-6 pb-8 w-full lg:w-1/1">
                <h1 class="mb-1">Choose User</h1>
                <v-select v-model="form.user_id" label="fullname" :reduce="user_for_select_option => user_for_select_option.id" :options="user_for_select_option"></v-select>
              </div>

              <div class="px-5  bg-gray-100  flex justify-start items-center">
                <loading-button ref="sbbtn" :loading="sending" class="btn-indigo" type="submit">Add User</loading-button>
              </div>
            </form>
          </div>
          <!--menghapus fitur add user untuk pengguna dosen-->
          
        </div>
      </div>
    </div>
    <div class="w-full mt-6 bg-gray-100 p-2 pb-6">
      <div class="flex justify-between bg-gray-300 rounded-lg mb-4 ">
        <span class=" font-semibold text-lg px-4 py-3">
          List Teachers
        </span>
      </div>

      <div v-for="user_in_group in gurus" :key="user_in_group.id" class="flex justify-between items-center mt-5">
        <div class="text-gray-800 text-center inline-flex items-center">
          <i class="fa fa-user mr-4" />
          <span class="text-base font-semibold text-gray-800 ml-2">{{ user_in_group.fullname }}</span>
          <span class="text-base font-semibold text-gray-800 ml-3"> - <b>({{ user_in_group.email }})</b></span>
        </div>

        <div v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4" class="cursor-pointer" @click="destroy(user_in_group.id,user_in_group.fullname)">
          <!--<span class="inline-block bg-red-200 hover:bg-red-700 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2">
            <icon name="trash" class="block w-6 h-6 fill-red-400" />
          </span>-->
        </div>
      </div>

      <div v-if="gurus.length === 0">
        <div class="bg-white shadow-lg rounded-lg hover:bg-gray-100 w-full px-4 py-4 mb-2">
          <div class="flex items-center justify-between">
            <h2 class="text-lg  text-gray-700 ">No Teachers Data</h2>
          </div>
        </div>
      </div>
    </div>

    <div class="w-full mt-6 bg-gray-100 p-2 pb-6">
      <div class="flex justify-between bg-gray-300 rounded-lg mb-4 ">
        <span class=" font-semibold text-lg px-4 py-3">
          List Students
        </span>
      </div>

      <div v-for="user_in_group in users_in_group" :key="user_in_group.id" class="flex justify-between items-center mt-5">
        <div class="text-gray-800 text-center inline-flex items-center">
          <i class="fa fa-user mr-4" />
          <span class="text-base font-semibold text-gray-800 ml-2">{{ user_in_group.fullname }}</span>
          <span class="text-base font-semibold text-gray-800 ml-3"> - <b>({{ user_in_group.email }})</b></span>
          <span class="inline-block bg-green-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3">
            <i class="fa fa-check " /> {{ status[user_in_group.is_active] }}
          </span>
        </div>

        <div class="cursor-pointer inline-flex items-center">
          <inertia-link v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4" class="px-6 py-4 flex items-center" :href="route('tartibpos_users.show',user_in_group.id)" tabindex="-1">
            <i class="fa fa-plus-circle" />
          </inertia-link>

          <inertia-link v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4" class="px-6 py-4 flex items-center" :href="route('tartibneg_users.show',user_in_group.id)" tabindex="-1">
            <i class="fa fa-minus-circle" />
          </inertia-link>
<!--menghapus fitur delete pada tabel list student oleh pengguna dosen-->
          <div v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 4" class="cursor-pointer" @click="destroy(user_in_group.id,user_in_group.fullname)">
            <span class="inline-block bg-red-200 hover:bg-red-700 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2">
              <icon name="trash" class="block w-6 h-6 fill-red-400" />
            </span>
          </div>
        </div>
      </div>

      <div v-if="users_in_group.length === 0">
        <div class="bg-white shadow-lg rounded-lg hover:bg-gray-100 w-full px-4 py-4 mb-2">
          <div class="flex items-center justify-between">
            <h2 class="text-lg  text-gray-700 ">No Students Data</h2>
          </div>
        </div>
      </div>
    </div>
    <modal-confirmation :showModal="modal.showModal" :titleModal="modal.titleModal" :descModal="modal.descModal" v-on:toogle="toggleModal" v-on:confirm="confirmModal"> </modal-confirmation>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import ModalConfirmation from '@/Shared/ModalConfirmation'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    ModalConfirmation,
  },

  props: {
    group: Object,
    users_in_group: Array,
    gurus: Array,
    user_for_select_option: Array,
    teacher_user_for_select_option: Array,
    teacher: Object,
    showDownloadExcel: Boolean,
  },
  remember: 'form',
  data() {
    return {
      status:{
        '-2' : 'Leave',
        '-1' : 'Rejected ',
        '0' : 'Pending',
        '1' : 'Active',
      },
      modal: {
        showModal: false,
        descModal: 'Are you sure you want to leave this group?',
        titleModal: 'Leave Group',
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
      if (confirm('Are you sure to delete data '+fullname+'?')) {
        this.$inertia.delete(this.route('group_users.destroy', id))
      }
    },
    toggleModal: function(){
      this.modal.showModal = !this.modal.showModal
    },
    confirmModal: function(){
      this.$inertia.replace(route('student-groups-exit', this.form.group_id)).then(() => this.modal.showModal = false)
    },
  },
}
</script>
