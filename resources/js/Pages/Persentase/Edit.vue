<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('persentase')">Percentage</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ namakategori }}
    </h1>
    <!-- <trashed-message v-if="persentase.deleted_at" class="mb-6" @restore="restore">
      Persentase ini telah dihapus.
    </trashed-message> -->
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            type="hidden"
            v-model="form.category_id"
            :errors="$page.errors.category_id"
          />
          <select-input v-model="form.task_type" :errors="$page.errors.task_type" class="pr-6 pb-8 w-full" label="Task Type">
            <option value="Tugas">Tugas</option>
            <option value="UTS">UTS</option>
            <option value="UAS">UAS</option>
            <option value="Perform">Perform</option>
            <option value="Sakit">Sakit</option>
            <option value="Izin">Izin</option>
            <option value="Alpha">Alpha</option>
          </select-input>
          <text-input
            v-model="form.persen"
            type="number"
            :errors="$page.errors.persen"
            class="pr-6 pb-8 w-full lg:w-full"
            label="Percentage"
            step="any"
          />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Percentage</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Percentage</loading-button>
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
    persentase: Object,
    kategori: Array,
    namakategori: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        id: this.persentase.id,
        persen: this.persentase.persen,
        task_type: this.persentase.task_type,
        category_id: this.persentase.category_id
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route('persentase.update', this.persentase.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this persentase?')) {
        this.$inertia.delete(this.route('persentase.destroy', this.persentase.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this persentase?')) {
        this.$inertia.put(this.route('persentase.restore', this.persentase.id))
      }
    },
  },
}
</script>
