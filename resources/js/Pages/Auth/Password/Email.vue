<template>
  <div class="p-6 bg-indigo-800 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
      <center>
        <logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
      </center>

      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-3xl">LMS AL-FITHRAH</h1>
          <div class="mx-auto mt-6 w-24 border-b-2" />
          <text-input v-model="form.email" :errors="$page.errors.email" class="mt-10" label="Email" autofocus autocapitalize="off" placeholder="" required />

        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-200 flex justify-between items-center">
          <a class="hover:underline" tabindex="-1" :href="route('login')">Login</a>
          <loading-button :loading="sending" class="btn-indigo" type="submit">Send Reset Password Link</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import LoadingButton from '@/Shared/LoadingButton'
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'

export default {
  metaInfo: { title: 'Reset Password' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
  },
  props: {
    errors: Object,
    email: String,
    token: String,
  },
  data() {
    return {
      sending: false,
      form: {
        email: this.email,
        token: this.token,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.post(this.route('password.send_email'), {
        email: this.form.email,

      }).then(() => this.sending = false)
    },
  },
}
</script>
