<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('group_users.show', group.id)">{{ group.classes }}</inertia-link> /
      List Positive Attitude
    </h1>
    <div v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4" class="mb-8">
      <inertia-link class="btn-indigo" :href="route('tartibpos_users.create', groupUser.id)">Add Positive Points</inertia-link>
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
            <form ref="form" @submit.prevent="submit">
              <!-- <div class="pr-6 pb-8 w-full lg:w-1/1">
                      <h1 class="mb-1">Pilih User</h1>
                      <v-select label="fullname" :reduce="user_for_select_option => user_for_select_option.id" v-model="form.user_id" :options="user_for_select_option"></v-select>
                    </div> -->
            </form>
          </div>
          <div v-else class="flex justify-end">
            <span class="bg-transparent cursor-pointer hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded"
                  @click="toggleModal()"
            >Leave</span>
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
          <th class="px-6 pt-6 pb-4">Name of Positive Attitude</th>
          <th class="px-6 pt-6 pb-4">Notes</th>
          <th class="px-6 pt-6 pb-4">Point</th>
        </tr>
        <tr
          v-for="tartibpos in tartibuser"
          :key="tartibpos.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibpos.date }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibpos.fullname }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibpos.kode_pelanggaran }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibpos.nama_pelanggaran }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibpos.catatan }}
            </div>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ tartibpos.poin }}
            </div>
          </td>
        </tr>
        <tr v-if="tartibuser.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="7">No positive attitude data.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {

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
      sending: false,
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
  },
}
</script>
