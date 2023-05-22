<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('detail.course.modules', form.id)">Presence Start Form</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ this.dataCourseModule.title }}
    </h1>
   
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 w-full">
          
         <p>Please click the Start button so students can carry out the Attendance process</p>
          <br>
          <loading-button :loading="sending" class="btn-indigo mb-5" type="submit">Start</loading-button>
        </div>
      </form>
    </div>
    
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  props: {
    dataCourseModule: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        id: this.dataCourseModule.id,
        course_id: this.dataCourseModule.course_id,
      },
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.post(this.route('presences.start', this.dataCourseModule.id), this.form)
        .then(() => this.sending = false)
    },
  
  },
}
</script>
