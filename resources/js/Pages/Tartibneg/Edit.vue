<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tartibs')">Tartibs</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.nama_pelanggaran }}
    </h1>
    <trashed-message v-if="tartib.deleted_at" class="mb-6" @restore="restore">
      Tartib has been removed.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
           <text-input
            v-model="form.kode_pelanggaran"
            :errors="$page.errors.kode_pelanggaran"
            class="pr-6 pb-8 w-full lg"
            label="Code Violation"
          />
          <text-input
            v-model="form.nama_pelanggaran"
            :errors="$page.errors.nama_pelanggaran"
            class="pr-6 pb-8 w-full lg"
            label="Name Violation"
          />
          <select-input
            v-model="form.kategori"
            :errors="$page.errors.kategori"
            class="pr-6 pb-8 w-full lg"
            label="Category Violation"
          >
            <!-- <option :value="null" /> -->
            <option value="POSITIF" selected>Positif</option>
            <option value="RINGAN" >Ringan</option>
            <option value="SEDANG" >Sedang</option>
            <option value="BERAT" >Berat</option>
            <option value="SANGAT_BERAT" >Sangat Berat</option>
          </select-input>

           <select-input
            v-model="form.jenis"
            :errors="$page.errors.jenis"
            class="pr-6 pb-8 w-full lg"
            label="Type Violation"
          >
            <!-- <option :value="null" /> -->
            <option value="POSITIF" >POSITIF</option>
            <option value="NEGATIF" selected>NEGATIF</option>
          </select-input>

          <text-input
            v-model="form.skor"
            :errors="$page.errors.skor"
            class="pr-6 pb-8 w-full lg"
            label="skor"
          />
          <select-input v-model="form.is_active" :errors="$page.errors.is_active" class="pr-6 pb-8 w-full lg" label="Status">
            <!-- <option :value="null" /> -->
            <option value="0">Not Active</option>
            <option value="1">Active</option>
          </select-input>

        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button v-if="!tartib.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Tartib</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Tartib</loading-button>
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
      title: `${this.form.name}`,
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
    tartib: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
       
        is_active: this.tartib.is_active,
        nama_pelanggaran: this.tartib.nama_pelanggaran,
        kode_pelanggaran: this.tartib.kode_pelanggaran,
        jenis: this.tartib.jenis,
        kategori: this.tartib.kategori,
        skor: this.tartib.skor,     

      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route('tartibs.update', this.tartib.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to remove this tartib?')) {
        this.$inertia.delete(this.route('tartibs.destroy', this.tartib.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this tartib?')) {
        this.$inertia.put(this.route('tartibs.restore', this.tartib.id))
      }
    },
  },
}
</script>
