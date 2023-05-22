<template>
  <div class="p-6 bg-indigo-1000 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
      <center>
        <logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
      </center>
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
          <h1 class="text-center font-extrabold text-3xl text-black">LMS AL-FITHRAH</h1>
          <div class="mx-auto mt-6 w-24 border-b-2" />
          <text-input v-model="form.username" :errors="$page.errors.username" class="mt-10" label="Username" autofocus autocapitalize="off" placeholder="input username" />

          <label class="form-label mt-6" :for="password">Password :</label>
          <div class="flex flex-wrap items-stretch w-full mb-4 relative">
            <input v-model="form.password" name="password" :type="passwordType" class="form-input flex-shrink flex-grow flex-auto leading-normal w-px flex-1 border h-10 border-grey-light rounded rounded-r-none px-3 relative" placeholder="input password">
            <div class="flex -mr-px">
              <span class="flex items-center leading-normal bg-grey-lighter rounded rounded-l-none border border-l-0 border-grey-light px-3 whitespace-no-wrap text-grey-dark text-sm hover:bg-gray-300" @click="showPassword()"><i :class="passwordType == 'password' ? 'fa fa-eye-slash' : 'fa fa-eye'" /></span>
            </div>
          </div>
          <!-- <text-input v-model="form.password" class="mt-6" label="Password" :type="passwordType" placeholder="input password" /> -->
          <!-- <span class="btn-bland fa fa-eye" @click="showPassword()"></span> -->
          <div class="flex justify-between ">
            <label class="mt-6 select-none flex items-center" for="remember">
              <input id="remember" v-model="form.remember" class="mr-1" type="checkbox">
              <span class="text-sm">Remember Me</span>
            </label>
          </div>
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-200 flex justify-between items-center">
          <a class="hover:underline" tabindex="-1" :href="route('password.request_link')">Forget password?</a>
          <loading-button :loading="sending" class="btn-indigo" type="submit">Login</loading-button>
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
  metaInfo: { title: 'Login' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
  },
  props: {
    errors: Object,
  },
  data() {
    return {
      passwordType: 'password',
      btnText: 'Show Password',
      sending: false,
      form: {
        // username: 'username',
        // password: 'password',
        remember: null,
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
      this.$inertia.post(this.route('login.attempt'), {
        username: this.form.username,
        password: this.form.password,
        remember: this.form.remember,
      }).then(() => this.sending = false)
    },
  },
}
</script>
