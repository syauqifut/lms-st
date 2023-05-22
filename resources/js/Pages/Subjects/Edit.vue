<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('subjects')">Subjects</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message v-if="subject.deleted_at" class="mb-6" @restore="restore">
      This Subject data has been deleted.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg" label="Subject Name" />
          <textarea-input v-model="form.description" :errors="$page.errors.description" class="pr-6 pb-8 w-full lg" label="Description" />
          <text-input
            v-model="form.subject_code"
            :errors="$page.errors.subject_code"
            class="pr-6 pb-8 w-full lg"
            label="Subject Code (empty if not available)"
          />
          <text-input
            v-model="form.sks"
            :errors="$page.errors.sks"
            class="pr-6 pb-8 w-full lg"
            label="SKS Score (empty if not available)"
          />
          <text-input
            v-model="form.subject_type"
            :errors="$page.errors.subject_type"
            class="pr-6 pb-8 w-full lg"
            label="Subject Type(empty if not available) , Ex : Wajib Program Study"
          />
          <select-input v-model="form.is_active" :errors="$page.errors.is_active" class="pr-6 pb-8 w-full lg" label="Status">
            <!-- <option :value="null" /> -->
            <option value="0">Not Active</option>
            <option value="1">Active</option>
          </select-input>

        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button v-if="!subject.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Subject</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Subject</loading-button>
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
    subject: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.subject.name,
        description: this.subject.description,
        is_active: this.subject.is_active,
        sks: this.subject.sks,
        subject_code: this.subject.subject_code,     
        subject_type: this.subject.subject_type,     

      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route('subjects.update', this.subject.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this subject?')) {
        this.$inertia.delete(this.route('subjects.destroy', this.subject.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this subject?')) {
        this.$inertia.put(this.route('subjects.restore', this.subject.id))
      }
    },
  },
}
</script>
