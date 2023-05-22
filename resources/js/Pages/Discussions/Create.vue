<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('discussions.index', module.id)">Discussion</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Discussion
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input v-model="form.course_module_id" type="hidden">
        <input v-model="form.addAgain" type="hidden">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.title"
            :errors="$page.errors.title"
            class="pr-6 pb-8 w-full lg"
            label="Topic Discussion"
          />

          <textarea-input
            v-model="form.discuss"
            :errors="$page.errors.discuss"
            class="pr-6 pb-8 w-full lg"
            label="Message"
          />

          <file-input v-model="form.file_attachment" :errors="$page.errors.file_attachment" class="pr-6 pb-8 w-full lg" type="file" label="File Attachment" />

          <select-input
            v-model="form.is_active"
            :errors="$page.errors.is_active"
            class="pr-6 pb-8 w-full lg"
            label="Status"
          >
            <option value="0">Not Active</option>
            <option value="1" selected>Active</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            class="btn-bland mr-2 cursor-pointer"
            @click="addAnother"
          >Create Discussion and Add New</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >
            Create Discussion
          </loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import TextInput from '@/Shared/TextInput'
import TextareaInput from '@/Shared/TextareaInput'
import FileInput from '@/Shared/FileInput'
import SelectInput from '@/Shared/SelectInput'

export default {
  metaInfo: { title: 'Create Discussion' },
  layout: Layout,
  components: {
    LoadingButton,
    TextInput,
    TextareaInput,
    FileInput,
    SelectInput,
  },
  props: {
    courseModule: Object,
  },
  remember: 'form',
  data() {
    return {
      module: this.courseModule,
      sending: false,
      form: {
        title: null,
        discuss: null,
        file_attachment: null,
        course_module_id: this.courseModule.id,
        is_active: 1,
        addAgain: false,
      },
    }
  },
  methods: {
    addAnother() {
      this.form.addAgain = true
      const btn = this.$refs.btnsubmit.$el
      btn.click()
    },
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('title', this.form.title || '')
      data.append('discuss', this.form.discuss || '')
      data.append('file_attachment', this.form.file_attachment || '')
      data.append('is_active', this.form.is_active || '')
      data.append('course_module_id', this.form.course_module_id || '')
      data.append('addAgain', this.form.addAgain || '')

      this.$inertia.post(this.route('discussions.store'), data).then(() => {
        this.sending = false
        this.form.is_active = 1
        this.form.addAgain = false
      })
    },
  },
}
</script>
