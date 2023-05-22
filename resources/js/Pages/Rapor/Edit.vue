<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('rapor.index')">Report</inertia-link>
        / {{ form.nama }} / 
        {{ form.subject }} {{subjectcode}}
    </h1>
    <trashed-message v-if="rapor.deleted_at" class="mb-6" @restore="restore">
      This Report data has been deleted.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input label="NIM" v-model="form.nim" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" />
          <text-input label="Name" v-model="form.nama" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" />
          <text-input label="Group" v-model="groupinfo" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-full" />
          <text-input label="Subject" v-model="subjectonly.subject" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-full" />
          <text-input label="Homeroom Teacher" v-model="form.walikelas" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" />
          <text-input label="Teacher" v-model="form.gurupengajar" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/2" />
          <!-- <text-input label="Tugas" v-model="form.tugas" disabled="disabled" class="pr-6 pb-8 w-full lg lg:w-1/4" /> -->
          <div class="pr-6 pb-8 w-full lg lg:w-1/4 leading-normal" >
            <label>Tugas: ({{persentugas}}%)</label>
            <text-input v-model="form.tugas" disabled="disabled"/>
          </div>
          <div class="pr-6 pb-8 w-full lg lg:w-1/4 leading-normal" >
            <label>UTS: ({{persenuts}}%)</label>
            <text-input v-model="form.uts" disabled="disabled"/>
          </div>
          <div class="pr-6 pb-8 w-full lg lg:w-1/4 leading-normal" >
            <label>UAS: ({{persenuas}}%)</label>
            <text-input v-model="form.uas" disabled="disabled"/>
          </div>
          <div class="pr-6 pb-8 w-full lg lg:w-1/4 leading-normal" >
            <label>Perform: ({{persenperform}}%)</label>
            <text-input v-model="form.perform" disabled="disabled"/>
          </div>
          <div class="pr-6 pb-8 w-full lg lg:w-1/3 leading-normal" >
            <label>Sakit: ({{persensakit}}%)</label>
            <text-input v-model="form.sakit" disabled="disabled"/>
          </div>
          <div class="pr-6 pb-8 w-full lg lg:w-1/3 leading-normal" >
            <label>Izin: ({{persenizin}}%)</label>
            <text-input v-model="form.izin" disabled="disabled"/>
          </div>
          <div class="pr-6 pb-8 w-full lg lg:w-1/3 leading-normal" >
            <label>Alpha: ({{persenalpha}}%)</label>
            <text-input v-model="form.alpha" disabled="disabled"/>
          </div>
        </div>
        <div v-if="authuser==1 || authuser==3|| authuser==4" class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Generate Report</loading-button>
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
      title: `${this.form.nama} - ${this.form.subject}`,
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
    rapor: Object,
    course: Object,
    subject: Object,
    subjectcode: Object,
    teacher: Object,
    user: Object,
    group: Array,
    groupuser: Array,
    courseuser: Array,
    tugas: Object,
    bagitugas: Object,
    uts: Object,
    uas: Object,
    perform: Object,
    sakit: Object,
    izin: Object,
    alpha: Object,
    userdata: Object,
    nilai: Object,
    groupinfo: Object,
    authuser:Object,

    persentugas : Object,
    persenuts : Object,
    persenuas : Object,
    persenperform : Object,
    persensakit : Object,
    persenizin : Object,
    persenalpha : Object,
  },
  remember: 'form',
  mounted() {
    console.log(this);
    this.myAttribute = this.$el.getAttribute('data-attribute-name');
  },
  data() {
    return {
      sending: false,
      form: {
        nim: this.userdata[0]['username'],
        nama: this.userdata[0]['fullname'],
        tugas: this.tugas/this.bagitugas || 0,
        uts: this.uts,
        uas: this.uas,
        perform: this.perform,
        sakit: this.sakit,
        izin: this.izin,
        alpha: this.alpha,
        nilai:this.nilai,
        huruf: null,
        kelas: this.groupuser[0]['classes'],
        walikelas: this.groupuser[0]['fullname'],
        subject: this.subject,
        gurupengajar: this.teacher[0]['fullname'],
        course_id: this.course.id,
        addAgain: false,
      },
      subjectonly:{
        subject: this.subject + ' (' + this.subjectcode + ')'
      }
    }
  },
  methods: {
    submit() {
      this.sending = true;
      this.$inertia.post(this.route("rapor.store"), this.form).then(() => {
        this.sending = false;
        this.form.addAgain = false;
      });
    },
    destroy() {
      if (confirm('Are you sure you want to delete this report?')) {
        this.$inertia.delete(this.route('rapor.destroy', this.rapor.id))
      }
    },
  },
}
</script>
