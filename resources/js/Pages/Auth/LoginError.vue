<template>
  <div class="p-6 bg-indigo-1000 min-h-screen flex justify-center items-center">
    <div class="w-full max-w-md">
      <div class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="px-10 py-12">
          <h1 class="text-center font-extrabold text-3xl text-black">YOU CAN'T LOGIN RIGHT NOW</h1>
          <div class="mx-auto mt-6 w-24 border-b-2" />
          <label class="form-label mt-6">Harap lunasi dahulu spp anda, mohon hubungi admin</label>
        </div>
        <div class="px-10 py-4 bg-gray-100 border-t border-gray-200 flex justify-between items-center">
          <inertia-link class="btn-indigo" :href="route('logout')" method="post">Back to Login</inertia-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingButton from '@/Shared/LoadingButton'
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'

export default {
  metaInfo: { title: 'Login Error' },
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
      sending: false,
      form: {
        remember: null,
      },
    }
  },
  methods: {
    
  },
}
</script>
