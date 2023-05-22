<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('access.list')">Access</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ access.name }}
    </h1>

    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10 ">
      <div class="w-full flex flex-wrap">

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
          
        </div>
      </div>
    </div>

    <div class="w-full mt-6 bg-gray-100 p-2 pb-6">
      <div class="flex justify-between bg-gray-300 rounded-lg mb-4 ">
        <span class=" font-semibold text-lg px-4 py-3">
          List User
        </span>
      </div>

      <div v-for="user_in_group in users_in_group" :key="user_in_group.id" class="flex justify-between items-center mt-5">
        <div class="text-gray-800 text-center inline-flex items-center">
          <i class="fa fa-user mr-4" />
          <span class="text-base font-semibold text-gray-800 ml-2">{{ user_in_group.fullname }}</span>
          <span class="text-base font-semibold text-gray-800 ml-3"> - <b>({{ user_in_group.email }})</b></span>
        </div>
          <div v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4" class="cursor-pointer" @click="destroy(user_in_group.id,user_in_group.fullname)">
            <span class="inline-block bg-red-200 hover:bg-red-700 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2">
              <icon name="trash" class="block w-6 h-6 fill-red-400" />
            </span>
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
    access: Object,
    users_in_group: Array,
    user_for_select_option: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        user_id : null,
        access_id: this.access.id,
      },
    }
  },
  methods: {
    submit() {

      this.sending = true
      var data = new FormData()

      data.append('user_id', this.form.user_id || '')
      data.append('access_id', this.form.access_id || '')

      this.$inertia.post(this.route('access.storeuser'), data)
        .then(() => this.sending = false)

    },
    destroy(id,fullname) {
      if (confirm('Are you sure to delete data '+fullname+'?')) {
        this.$inertia.delete(this.route('access.destroyuser', id))
      }
    },
  },
}
</script>
