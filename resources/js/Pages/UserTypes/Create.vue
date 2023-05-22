<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('usertypes')">{{menuName}}</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Tambah Data
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit" ref="form">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
         

            <text-input type="text" v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/1" label="Usertype Name" />
        </div>
        
       
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
         <loading-button ref="sbbtn" :loading="sending" class="btn-indigo" type="submit">Create {{menuName}}</loading-button>
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

export default {
  metaInfo: { title: '' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
  },
  props: {
    users: Array,
    menuName: String,    
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        classes: null,
        year: '2020',
        academicterms: null,
        addAgain: false,
        mainteacher: null,
      },
    }
  },
  methods: {
    newaa(){
      this.form.addAgain = true
      const btn = this.$refs.sbbtn.$el
      btn.click()
    },
    submit() {
      this.sending = true
      this.$inertia.post(this.route('usertypes.store'), this.form)
        .then(() => 
        {
          this.sending = false
          this.form.classes = null
          this.form.year = '2020'
          this.form.academicterms = null
          this.form.addAgain = false
          this.form.mainteacher = null
        })
    },
  },
}
</script>
