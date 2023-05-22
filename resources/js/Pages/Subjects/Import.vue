<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Import Subjects</h1>
    <div class="mb-6 flex justify-between items-center">
      <a class="btn-indigo" :href="route('subjects.template')">
        <span>Download Sample</span>
      </a>
    </div>

    <div class="lg:flex justify-evenly">
      <div class="flex-1 m-2">
        <form @submit.prevent="submit">
          <div class="bg-white rounded shadow overflow-hidden w-full">
            <div class="flex w-full h-64 items-center justify-center bg-grey-lighter">
              <label
                class="w-64 flex flex-col items-center px-4 py-6 bg-gray-200 text-blue rounded-lg shadow-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-blue hover:text-white"
              >
                <svg
                  class="w-8 h-8"
                  fill="currentColor"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                >
                  <path
                    d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z"
                  />
                </svg>
                <span v-if="!form.subject_data" class="mt-2 text-base leading-normal">Select a file</span>
                <span v-else class="mt-2 font-bold text-teal-300 leading-normal">File Ready</span>
                <file-input
                  v-model="form.subject_data"
                  type="file"
                  class="hidden"
                  name="subject_data"
                  :errors="$page.errors.subject_data"
                />
              </label>
            </div>
          </div>
          <div class="bg-white rounded shadow overflow-hidden p-6">
            <div class="h-full">
              <textarea-input
                v-model="form.description"
                :errors="$page.errors.description"
                class="pr-6 pb-8 w-full lg"
                label="Import Description"
              />
            </div>
            <div class="flex justify-end items-center">
              <loading-button :loading="sending" class="btn-indigo" type="submit">Import Subject</loading-button>
            </div>
          </div>
        </form>
      </div>
      <div class="flex-1 lg:mt-2 mt-3">
        <div class="bg-white mx-auto p-6">
          <h1 class="text-3xl mb-5">
            Panduan Import Subjects
          </h1>
          <div class="mb-4">
            <b-button v-b-toggle.collapse-1 variant="primary" class="w-full">
              <div
                class="flex items-center justify-between bg-gray-200 pl-3 pr-2 py-3 w-full rounded text-gray-600 font-bold cursor-pointer hover:bg-gray-300"
              >
                Apa yang harus dilakukan ?
                <span
                  class="h-6 w-6 flex items-center justify-center text-teal-500"
                >
                  <svg
                    class="w-3 h-3 fill-current"
                    viewBox="0 -192 469.33333 469"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="m437.332031.167969h-405.332031c-17.664062 0-32 14.335937-32 32v21.332031c0 17.664062 14.335938 32 32 32h405.332031c17.664063 0 32-14.335938 32-32v-21.332031c0-17.664063-14.335937-32-32-32zm0 0"
                    />
                  </svg>
                </span>
              </div>
            </b-button>
            <b-collapse id="collapse-1" class="mt-2">
              <div class="p-3">
                <p class="text-gray-600">
                  Ketikkan nama dan deskripsi subject pada excel sesuai dengan contoh sample
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
import Layout from '@/Shared/Layout'
import TextareaInput from '@/Shared/TextareaInput'
import FileInput from '@/Shared/FileInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  metaInfo: { title: 'Subjects' },
  layout: Layout,
  components: {
    TextareaInput,
    LoadingButton,
    FileInput,
  },
  props: {
    // subjects: Object,
    // filters: Object,
  },
  data() {
    return {
      sending: false,
      form: {
        subject_data: null,
        description: null,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      var data = new FormData()
      data.append('subject_data', this.form.subject_data || '')
      data.append('description', this.form.description || '')
      this.$inertia.post(this.route('subjects.template'), data).then(() => {
        this.sending = false
        this.form.subject_data = null
        this.form.description = null
      })
    },
  },
}
</script>
