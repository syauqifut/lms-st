<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('detail.course.modules', form.id)">Student Attendance Form</inertia-link>
    </h1>
   
    <h1 class="mb-8 font-bold text-xl"> 
      Materi : {{ this.dataCourseModule.title }}
    </h1>
    <div  v-if="dataCourseModule.actual_start_at" class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 w-full">
          
         <p>Please click the Presence Button</p>
          <br>
          <loading-button :loading="sending" class="btn-indigo mb-5" type="submit">Presence</loading-button>
        </div>
      </form>
    </div>
    <div v-else >
      <p>You can't do the attendance process because the class hasn't started yet</p>
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
      this.$inertia.post(this.route('presences.present', this.dataCourseModule.id), this.form)
        .then(() => this.sending = false)
    },
  
  },
}
</script>
