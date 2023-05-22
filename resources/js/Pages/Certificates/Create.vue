<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('certificates.index', user.id)">Berkas</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Tambah Berkas
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input v-model="form.addAgain" type="hidden">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.nama"
            :errors="$page.errors.nama"
            class="pr-6 pb-8 w-full lg"
            label="Certificate Name"
          />

          <textarea-input
            v-model="form.keterangan"
            :errors="$page.errors.keterangan"
            class="pr-6 pb-8 w-full lg"
            label="Description"
          />

          <file-input v-model="form.file" :errors="$page.errors.file" class="pr-6 pb-8 w-full lg" type="file" label="File" />

          <text-input
            v-model="form.tahun_sertifikat"
            type="number"
            :errors="$page.errors.tahun_sertifikat"
            class="pr-6 pb-8 w-full lg"
            label="Certificate Year"
          />

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Description Certificate:</h1>
            <v-select v-model="form.desc_certificate" :options="['National','International']" />
          </div>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <!-- <span
            class="btn-bland mr-2 cursor-pointer"
            @click="addAnother"
          >Tambah Sertifikat dan Tambah Baru</span> -->
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >
            Add File
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

export default {
  metaInfo: { title: 'Buat Berkas' },
  layout: Layout,
  components: {
    LoadingButton,
    TextInput,
    TextareaInput,
    FileInput,
  },
  props: {
    user: Object,
  },
  remember: 'form',
  data() {
    return {

      sending: false,
      form: {
        nama: null,
        keterangan: null,
        file: null,
        tahun_sertifikat: null,
        desc_certificate: null,
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
      data.append('nama', this.form.nama || '')
      data.append('keterangan', this.form.keterangan || '')
      data.append('file', this.form.file || '')
      data.append('tahun_sertifikat', this.form.tahun_sertifikat || '')
      data.append('desc_certificate', this.form.desc_certificate || '')
      data.append('addAgain', this.form.addAgain || '')

      this.$inertia.post(this.route('certificates.store'), data).then(() => {
        this.sending = false
        this.form.addAgain = false
      })
    },
  },
}
</script>
