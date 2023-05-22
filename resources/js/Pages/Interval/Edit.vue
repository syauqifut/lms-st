<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('interval.index')">Interval</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.id }}
    </h1>
    <trashed-message v-if="interval.deleted_at" class="mb-6" @restore="restore">
      This Interval data has been deleted.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <!-- <text-input v-model="form.id" :errors="$page.errors.id" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Id interval" /> -->
          <text-input v-model="form.minmark" :errors="$page.errors.minmark" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Min Mark" />
          <text-input v-model="form.maxmark" :errors="$page.errors.maxmark" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Max Mark" />
          <text-input v-model="form.minavg" :errors="$page.errors.minavg" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Min Average" />
          <text-input v-model="form.maxavg" :errors="$page.errors.maxavg" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Max Average" />
          <!-- <text-input v-model="form.status" :errors="$page.errors.status" class="pr-6 pb-8 w-full lg" label="Status" /> -->
          <text-input v-model="form.alphabet" :errors="$page.errors.alphabet" class="pr-6 pb-8 w-full" label="Alphabet" />
          <!-- <text-input v-model="form.level_id" :errors="$page.errors.status" class="pr-6 pb-8 w-full lg lg:w-1/2" label="Tingkat" /> -->
          <select-input v-model="form.status" :errors="$page.errors.status" class="pr-6 pb-8 w-full" label="Status">
            <option :value="null" />
            <option value="Lulus">Pass</option>
            <option value="Tidak Lulus">Not pass</option>
          </select-input>
          <div class="pr-6 pb-8 w-full">
            <h1 class="mb-1">Level</h1>
            <v-select label="title" :reduce="level => level.id" v-model="form.level_id" :options="level"></v-select>
          </div>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Interval</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Interval</loading-button>
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
    interval: Object,
    level: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        id: this.interval.id,
        minmark: this.interval.minmark,
        maxmark: this.interval.maxmark,
        minavg: this.interval.minavg,
        maxavg: this.interval.maxavg,
        alphabet: this.interval.alphabet,
        status: this.interval.status,
        level_id: this.interval.level_id
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route('interval.update', this.interval.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this interval?')) {
        this.$inertia.delete(this.route('interval.destroy', this.interval.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this interval?')) {
        this.$inertia.put(this.route('interval.restore', this.interval.id))
      }
    },
  },
}
</script>
