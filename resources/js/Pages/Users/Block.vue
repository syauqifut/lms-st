<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Import Users Block</h1>
    <div class="mb-6 flex justify-between items-center">
      
      <a class="btn-indigo" :href="route('users.templateuserblock')">
        <span>Download Sample</span>
      </a>
    </div>

    <div class="lg:flex justify-evenly">
      <div class="flex-1 m-2">
        <form @submit.prevent="submit">
          <div class="bg-white rounded shadow overflow-hidden w-full">
            <div class="flex w-full h-64 items-center justify-center bg-grey-lighter">
                <label class="w-64 flex flex-col items-center px-4 py-6 bg-gray-200 text-blue rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-blue hover:text-white">
                    <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                    </svg>
                    <span class="mt-2 text-base leading-normal" v-if="!form.user_data">Select a file</span>
                    <span class="mt-2 font-bold text-teal-300 leading-normal" v-else>File Ready</span>
                    <file-input type='file' class="hidden" name="user_data" v-model="form.user_data" :errors="$page.errors.user_data" />
                </label>
            </div>
            
          </div>
          <div class=" bg-white rounded shadow overflow-hidden p-6">
              <div class="h-full">
              <textarea-input
                v-model="form.description"
                :errors="$page.errors.description"
                class="pr-6 pb-8 w-full lg"
                label="Import Description"
              />
            </div>
            <div class="flex justify-end items-center">
              <loading-button :loading="sending" class="btn-indigo" type="submit">Import Users</loading-button>
            </div>
          </div>
          </form>
      </div>
      <div class="flex-1 lg:mt-2 mt-3">
        <div class="bg-white mx-auto p-6">
          <div class="mb-4">
            <b-button v-b-toggle.collapse-1 variant="primary" class="w-full">
              <div class="flex items-center justify-between bg-gray-200 pl-3 pr-2 py-3 w-full rounded text-gray-600 font-bold cursor-pointer hover:bg-gray-300">
                Bagaimana menentukan user yang di blok?
                <span class="h-6 w-6 flex items-center justify-center text-teal-500">
                  <svg class="w-3 h-3 fill-current" viewBox="0 -192 469.33333 469" xmlns="http://www.w3.org/2000/svg"><path d="m437.332031.167969h-405.332031c-17.664062 0-32 14.335937-32 32v21.332031c0 17.664062 14.335938 32 32 32h405.332031c17.664063 0 32-14.335938 32-32v-21.332031c0-17.664063-14.335937-32-32-32zm0 0"/></svg>
                </span>
              </div>
            </b-button>
            <b-collapse id="collapse-1" class="mt-2">
              <div class="p-3">
              <p class="text-gray-600">
                Inputkan username atau NIK siswa di tabel excel sesuai contoh, inputan untuk siswa yang belum melunasi SPP
              </p>
              </div>
            </b-collapse>
          </div>
        </div>
      </div>
    </div>

    <div class="w-full mt-6 bg-gray-100 p-2 pb-6">
      <div class="flex justify-between bg-gray-300 rounded-lg mb-4 ">
        <span class=" font-semibold text-lg px-4 py-3">
          List of Blocked Students
        </span>
      </div>

      <div v-for="userblock in userblock" :key="userblock.id" class="flex justify-between items-center mt-5">
        <div class="text-gray-800 text-center inline-flex items-center">
          <i class="fa fa-user mr-4" />
          <span class="text-base font-semibold text-gray-800 ml-2">{{ userblock.username }}</span>
          <span class="text-base font-semibold text-gray-800 ml-3"> - <b>{{ userblock.fullname }}</b></span>
          <span v-if="userblock.status=='0'" class="inline-block bg-green-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3">
            <i class="fa fa-check " /> {{ status[userblock.status] }}
          </span>
          <span v-if="userblock.status=='1'" class="inline-block bg-red-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3">
            <i class="fa fa-check " /> {{ status[userblock.status] }}
          </span>
        </div>

        <div class="cursor-pointer inline-flex items-center">
          <div v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4" class="cursor-pointer" @click="destroy(userblock.id,userblock.fullname)">
            <span v-if="userblock.status=='1'" class="inline-block bg-green-200 hover:bg-green-700 rounded-full px-3 py-2 text-lg font-normal text-gray-700 mr-2">
              <i class="fa fa-unlock" aria-hidden="true"></i>
            </span>
          </div>
        </div>
      </div>

      <div v-if="userblock.length === 0">
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
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import TextareaInput from "@/Shared/TextareaInput"
import FileInput from '@/Shared/FileInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  metaInfo: { title: 'User to Block' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
    TextareaInput,
    LoadingButton,
    FileInput,
  },
  props: {
    userblock: Array,
  },
  data() {
    return {
      status:{
        '0' : 'Unblocked',
        '1' : 'Blocked',
      },
      sending: false,
      form: {
        user_data: null,
        description: null,
      },
    }
  },
  watch: {
    
  },
  methods: {
    submit() {
      this.sending = true
      var data = new FormData()
      data.append('user_data', this.form.user_data || '')
      data.append('description', this.form.description || '')
      this.$inertia.post(this.route('users.templateuserblock'), data)
        .then(() => {
          this.sending = false
          this.form.user_data = null
          this.form.description = null
          })
    },
    destroy(id,fullname) {
      if (confirm('Are you sure to unblock '+fullname+'?')) {
        this.$inertia.delete(this.route('users.destroyblock', id))
      }
    },
  },
}
</script>
