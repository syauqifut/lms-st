<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('certificates.index', certificate.user_id)"
      >
        Berkas
      </inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.nama }}
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.nama"
            :errors="$page.errors.nama"
            class="pr-6 pb-8 w-full lg"
            label="Nama Berkas"
          />

          <textarea-input
            v-model="form.keterangan"
            :errors="$page.errors.keterangan"
            class="pr-6 pb-8 w-full lg"
            label="Keterangan"
          />

          <file-input v-model="form.file" :errors="$page.errors.file" class="pr-6 pb-8 w-full lg" type="file" label="File" />

          <text-input
            v-model="form.tahun_sertifikat"
            type="number"
            :errors="$page.errors.tahun_sertifikat"
            class="pr-6 pb-8 w-full lg"
            label="Tahun Berkas"
          />

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Description Certificate:</h1>
            <v-select v-model="form.desc_certificate" :options="['National','International']" />
          </div>

          <a :href="route('certificates.download_file', certificate.id)" class="block w-full text-blue-500 mb-5 text-lg hover:underline" target="_blank">File Sertifikat</a>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button
            class="text-red-600 hover:underline"
            tabindex="-1"
            type="button"
            @click="destroy"
          >
            Hapus Berkas
          </button>
          <loading-button
            :loading="sending"
            class="btn-indigo ml-auto"
            type="submit"
          >
            Perbarui Berkas
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
  metaInfo: { title: 'Edit Sertifikat' },
  layout: Layout,
  components: {
    LoadingButton,
    TextInput,
    TextareaInput,
    FileInput,
  },
  remember: 'form',
  props: {
    certificate: Object,
  },
  data() {
    return {
      sending: false,
      file_src: '/files/certificates/' + this.certificate.file,
      form: {
        nama: this.certificate.nama,
        keterangan: this.certificate.keterangan,
        desc_certificate: this.certificate.desc_certificate,
        file: null,
        tahun_sertifikat: this.certificate.tahun_sertifikat,
      },
    }
  },
  methods: {
    destroy() {
      if (confirm('Apakah kamu yakin ingin menghapus sertifikat ini?')) {
        this.$inertia.delete(this.route('certificates.destroy', this.certificate.id))
      }
    },
    // restore() {
    //   if (confirm('Apakah kamu yakin ingin me-restore course ini?')) {
    //     this.$inertia.put(this.route('courses.restore', this.course.id))
    //   }
    // },
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('nama', this.form.nama || '')
      data.append('keterangan', this.form.keterangan || '')
      data.append('file', this.form.file || '')
      data.append('tahun_sertifikat', this.form.tahun_sertifikat || '')
      data.append('desc_certificate', this.form.desc_certificate || '')
      data.append('_method', 'put')

      this.$inertia
        .post(this.route('certificates.update', this.certificate.id), data)
        .then(() => {
          this.sending = false
          this.form.file = null
        })
    },
  },
}
</script>
