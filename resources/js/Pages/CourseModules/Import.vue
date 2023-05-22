<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('course-modules.get_by_course', course.id)">Learning</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Import {{name}}
    </h1>
    <div class="mb-6 flex justify-between items-center">

      <a class="btn-indigo" :href="route( base_route +'.template')">
        <span>Download Sample</span>
      </a>
      <a class="btn-indigo" :href="route('export-master')">
        <span>Download master</span>
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
                    <span class="mt-2 text-base leading-normal" v-if="!form.data">Select a file</span>
                    <span class="mt-2 font-bold text-teal-300 leading-normal" v-else>File Ready</span>
                    <file-input type='file' class="hidden" name="data" v-model="form.data" :errors="$page.errors.data" />
                </label>
            </div>

          </div>
          <div class=" bg-white rounded shadow overflow-hidden p-6">
              <div class="h-full">
              <textarea-input
                v-model="form.description"
                :errors="$page.errors.description"
                class="pr-6 pb-8 w-full lg"
                label="Description Import"
              />
            </div>
            <div class="flex justify-end items-center">
              <loading-button :loading="sending" class="btn-indigo" type="submit">Import {{name}}</loading-button>
            </div>
          </div>
          </form>
      </div>
      <div class="flex-1 lg:mt-2 mt-3">
        <div class="bg-white mx-auto p-6">
          <div class="mb-4">
            <b-button v-b-toggle.collapse-1 variant="primary" class="w-full">
              <div class="flex items-center justify-between bg-gray-200 pl-3 pr-2 py-3 w-full rounded text-gray-600 font-bold cursor-pointer hover:bg-gray-300">
                Ketentuan Input data keseluruhan
                <span class="h-6 w-6 flex items-center justify-center text-teal-500">
                  <svg class="w-3 h-3 fill-current" viewBox="0 -192 469.33333 469" xmlns="http://www.w3.org/2000/svg"><path d="m437.332031.167969h-405.332031c-17.664062 0-32 14.335937-32 32v21.332031c0 17.664062 14.335938 32 32 32h405.332031c17.664063 0 32-14.335938 32-32v-21.332031c0-17.664063-14.335937-32-32-32zm0 0"/></svg>
                </span>
              </div>
            </b-button>
            <b-collapse id="collapse-1" class="mt-2">
              <div class="p-3">
              <p class="text-gray-600">
                Input terdiri dari 2 kolom sebagai berikut: <br>
                course = course dari course users, harus sesuai dengan data course 'title' pada database <br>
                username = user yang bersangkutan dengan courses, harus sesuai dengan data 'username' pada database <br>
                <hr class="my-2">
                Untuk mengetahui data-data pada database dapat mengakses pada tombol <a :href="route('export-master')"> "Download Master" </a>
              </p>
              </div>
            </b-collapse>
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
  metaInfo: { title: 'Import Learning Modules' },
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
    course: Object,
    // filters: Object,
  },
  data() {
    return {
      name: 'Learning Modules',
      base_route: 'course-modules',
      sending: false,
      form: {
        data: null,
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
      data.append('data', this.form.data || '')
      data.append('description', this.form.description || '')
      this.$inertia.post(this.route(this.base_route +'.template'), data)
        .then(() => {
          this.sending = false
          this.form.data = null
          this.form.description = null
          })
    },
  },
}
</script>
