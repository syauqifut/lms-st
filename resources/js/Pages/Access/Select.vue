<template>
  <div class="p-6 bg-indigo-1000 min-h-screen flex justify-center items-center">
    <!-- <div class="w-full max-w-4xl"> -->
    <!-- <div class="w-1/2"> -->
    <div class="inline-block">
      <!-- <div class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden"> -->
      <div class="mt-8 overflow-hidden">
        <div class="px-10">
          <h1 class="text-center text-4xl text-white mb-12">Select Access</h1>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow-xl overflow-hidden">
      <!-- <div class="mt-8 overflow-hidden"> -->
        <div class="px-10 py-2 flex justify-center">
          <div v-for="menu in menus" :key="menu.id" class="inline-flex place-items-center text-white">
            <!-- <inertia-link class="flex-1 box-border h-32 w-32 m-4 border rounded" :href="route(menu.route)"> -->
            <inertia-link class="flex-1 box-border h-32 w-32 m-4 bg-indigo-1000 flex text-center items-center justify-center text-xl border rounded" :href="route(menu.route)">
              {{ menu.name }}
            </inertia-link>
          </div>
          <div v-if="menus.length === 0" class="inline-flex place-items-center h-32 flex text-indigo-1000 text-center items-center justify-center text-xl">
            You don't have any Access
          </div>
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
  metaInfo: { title: 'Select Access' },
  components: {
    LoadingButton,
    Logo,
    TextInput,
  },
  props: {
    menus: Object,
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
