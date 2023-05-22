<template>
  <div class="p-6 bg-indigo-800 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
      <center>
        <logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
      </center>
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="submit">
        <input v-model="form.token" type="hidden" name="token">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-3xl">LMS AL-FITHRAH</h1>

          <div class="mx-auto mt-6 w-24 border-b-2" />
          <text-input v-model="form.email" :errors="$page.errors.email" class="mt-10" label="Email" required readonly/>
          <text-input v-model="form.password" :errors="$page.errors.password" class="mt-10" label="Password" type="password" required />
          <text-input v-model="form.password_confirmation" :errors="$page.errors.password_confirmation" class="mt-10" label="Confirm Password" type="password" required />
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-200 flex justify-between items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Update Password</loading-button>
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
  metaInfo: { title: 'Update Password' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
  },
  props: {
    errors: Object,
    dataReset: Object,
  },
  data() {
    return {
      passwordType: 'password',
      btnText: 'Show Password',
      sending: false,
      form: {
        email: this.dataReset.email,
        // password: 'password',
      },
    }
  },
  methods: {
    showPassword() {
      if(this.passwordType === 'password') {
        this.passwordType = 'text'
        // this.btnText = 'Hide Password'
      } else {
        this.passwordType = 'password'
        // this.btnText = 'Show Password'
      }
    },
    submit() {
      this.sending = true
      this.$inertia.post(this.route('password.update_password'), {
        email: this.form.email,
        password: this.form.password,
        password_confirmation: this.form.password_confirmation,
        token: this.dataReset.token,
      }).then(() => this.sending = false)
    },
  },
}
</script>
