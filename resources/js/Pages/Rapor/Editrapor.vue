<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('rapor.create')">Report</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.id }}
    </h1>
    <trashed-message v-if="rapor.deleted_at" class="mb-6" @restore="restore">
      This Report data has been deleted.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.nim" :errors="$page.errors.nim" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" label="NIM" />
          <text-input v-model="form.nama" :errors="$page.errors.nama" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Name" />
          <text-input v-model="groupinfo" :errors="$page.errors.kelas" disabled="disabled" class="pr-6 pb-8 w-full lg" label="Group" />
          <text-input v-model="subjectinfo" :errors="$page.errors.subject" disabled="disabled" class="pr-6 pb-8 w-full lg" label="Subject" />
          <text-input v-model="form.walikelas" :errors="$page.errors.walikelas" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Homeroom Teacher" />
          <text-input v-model="form.gurupengajar" :errors="$page.errors.gurupengajar" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Teacher" />
          <text-input v-model="form.tugas" :errors="$page.errors.tugas" class="pr-6 pb-8 w-full lg lg:w-1/4" label="Tugas" />
          <text-input v-model="form.uts" :errors="$page.errors.uts" class="pr-6 pb-8 w-full lg lg:w-1/4" label="UTS" />
          <text-input v-model="form.uas" :errors="$page.errors.uas" class="pr-6 pb-8 w-full lg lg:w-1/4" label="UAS" />
          <text-input v-model="form.perform" :errors="$page.errors.perform" class="pr-6 pb-8 lg lg:w-1/4" label="Perform" />
          <text-input v-model="form.sakit" :errors="$page.errors.sakit" class="pr-6 pb-8 w-full lg lg:w-1/3" label="Sakit" />
          <text-input v-model="form.izin" :errors="$page.errors.izin" class="pr-6 pb-8 w-full lg lg:w-1/3" label="Izin" />
          <text-input v-model="form.alpha" :errors="$page.errors.alpha" class="pr-6 pb-8 w-full lg lg:w-1/3" label="Alpha" />
          <text-input v-model="form.nilai" :errors="$page.errors.nilai" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Score" />
          <text-input v-model="form.huruf" :errors="$page.errors.huruf" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Alphabet" />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Report</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Report</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TextareaInput from '@/Shared/TextareaInput'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo() {
    return {
      title: `${this.form.id}`,
    }
  },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    TrashedMessage,
  },
  props: {
    rapor: Object,
    level: Array,
    tugas: Object,
    bagitugas: Object,
    uts: Object,
    uas: Object,
    perform: Object,
    groupinfo: Object,
    subjectinfo: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        id: this.rapor.id,
        nim: this.rapor.nim,
        nama: this.rapor.nama,
        kelas: this.rapor.kelas,
        walikelas: this.rapor.walikelas,
        subject: this.rapor.subject,
        gurupengajar: this.rapor.gurupengajar,
        tugas: this.tugas / this.bagitugas || 0,
        uts: this.uts,
        uas: this.uas,
        perform: this.perform,
        sakit: this.rapor.sakit,
        izin: this.rapor.izin,
        alpha: this.rapor.alpha,
        nilai: this.rapor.nilai,
        huruf: this.rapor.huruf,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route('rapor.updaterapor', this.rapor.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this report?')) {
        this.$inertia.delete(this.route('rapor.destroy', this.rapor.id))
      }
    },
    restore() {
      if (confirm('Apakah kamu yakin ingin me-restore rapor ini?')) {
        this.$inertia.put(this.route('rapor.restore', this.rapor.id))
      }
    },
  },
}
</script>
