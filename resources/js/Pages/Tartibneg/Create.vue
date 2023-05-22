<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <!-- <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tartibs')">Tartibs</inertia-link> -->
      <!-- <span class="text-indigo-400 font-medium" /> Add Violation -->
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tartibneg_users.show', form.group_user_id)">Add Violation </inertia-link>
      <span class="text-indigo-400 font-medium" /> / {{ user.fullname }}
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input v-model="form.group_user_id" type="hidden">
        <input v-model="form.user_id" type="hidden">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.date"
            type="date"
            :errors="$page.errors.date"
            class="pr-6 pb-8 w-full lg"
            label="Date Violation"
          />
          <div class="pr-6 pb-8 w-full lg">
            <h1 class="mb-1">Violation :</h1>
            <v-select v-model="form.tartib_id" label="name" :reduce="tartibs => tartibs.id" :options="tartibs" :errors="$page.errors.tartib_id" />
          </div>
          <select-input
            v-model="form.userlapor_id"
            :errors="$page.errors.userlapor_id"
            class="pr-6 pb-8 w-full lg"
            label="Reporter"
          >
            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id" >{{ teacher.fullname }}</option>
          </select-input>
           <textarea-input
            v-model="form.catatan"
            :errors="$page.errors.catatan"
            class="pr-6 pb-8 w-full lg"
            label="Notes"
          />
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >
            Add Violation
          </loading-button>
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
import TextareaInput from "@/Shared/TextareaInput";

export default {
  metaInfo: { title: 'Add Violation' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  remember: 'form',
  props: {
    groupUserId: String,
    user: Object,
    teachers: Array,
    tartibs: Array,
    loginid: Array,
  },
  data() {
    return {
      sending: false,
      form: {
        group_user_id: this.groupUserId,
        user_id: this.user.id,
        userlapor_id : this.loginid,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.post(this.route('tartibneg_users.store'), this.form).then(() => {
        this.sending = false
        this.form.userlapor_id = null
        this.form.tartib_id = null
        this.form.date = null
        this.form.catatan = null
      })
    },
  },
}
</script>
