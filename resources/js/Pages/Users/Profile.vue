<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <!-- <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('users.userprofile', user.id)">{{info}}</inertia-link> -->
      Detail Profile of {{user.first_name}}
    </h1>
    <div class="mb-8">
      <inertia-link class="btn-indigo" :href="route('users.userprofile', user.id)">Main Profile</inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit" ref="form">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.no_induk" :errors="$page.errors.no_induk" class="pr-6 pb-8 w-1/2" label="Nomor Induk" />
          <text-input v-model="form.nisn" :errors="$page.errors.nisn" class="pr-6 pb-8 w-1/2" label="NISN" />
          <text-input v-model="form.nik" :errors="$page.errors.nik" class="pr-6 pb-8 w-1/2" label="NIK" />
          <text-input v-model="form.kps" :errors="$page.errors.kps" class="pr-6 pb-8 w-1/2" label="KPS" />
          <text-input v-model="form.kip" :errors="$page.errors.kip" class="pr-6 pb-8 w-1/2" label="KIP" />
          <text-input v-model="form.kks" :errors="$page.errors.kks" class="pr-6 pb-8 w-1/2" label="KKS" />
          <text-input v-model="form.pkh" :errors="$page.errors.pkh" class="pr-6 pb-8 w-1/2" label="PKH" />
          <text-input v-model="form.no_kk" :errors="$page.errors.no_kk" class="pr-6 pb-8 w-1/2" label="No. KK" />
          <div class="pr-6 pb-8 w-full">
            <h1 class="mb-1">Region</h1>
            <v-select :label="label" @search="fetchOptions" :reduce="country => country.id" v-model="form.prov" :options="arKodepos"
              ></v-select>
          </div>
          <!-- <text-input v-model="form.prov" :errors="$page.errors.prov" class="pr-6 pb-8 w-1/2" label="Provinsi" />
          <text-input v-model="form.kec" :errors="$page.errors.kec" class="pr-6 pb-8 w-1/2" label="Kecamatan" /> -->
          <text-input v-model="form.nm_ayah" :errors="$page.errors.nm_ayah" class="pr-6 pb-8 w-1/2" label="Father's Name" />
          <text-input v-model="form.ktp_ayah" :errors="$page.errors.ktp_ayah" class="pr-6 pb-8 w-1/2" label="Father's Identity" />
          <select-input v-model="form.pend_ayah" :errors="$page.errors.pend_ayah" class="pr-6 pb-8 w-full lg:w-1/2" label="Father's Education">
            <option v-for="p in pendidikans" :key="p.id"  :value="p.id">{{p.nama}}</option>
          </select-input>
          <select-input v-model="form.krj_ayah" :errors="$page.errors.krj_ayah" class="pr-6 pb-8 w-full lg:w-1/2" label="Father's Occupation">
            <option v-for="p in pekerjaans" :key="p.id"  :value="p.id">{{p.name}}</option>
          </select-input>
          <text-input v-model="form.nm_ibu" :errors="$page.errors.nm_ibu" class="pr-6 pb-8 w-1/2" label="Mother's Name" />
          <text-input v-model="form.ktp_ibu" :errors="$page.errors.ktp_ibu" class="pr-6 pb-8 w-1/2" label="Mother's Identity" />
          <select-input v-model="form.pend_ibu" :errors="$page.errors.pend_ibu" class="pr-6 pb-8 w-full lg:w-1/2" label="Mother's Education">
            <option v-for="p in pendidikans" :key="p.id"  :value="p.id">{{p.nama}}</option>
          </select-input>
          <select-input v-model="form.krj_ibu" :errors="$page.errors.krj_ibu" class="pr-6 pb-8 w-full lg:w-1/2" label="Mother's Occupation">
            <option v-for="p in pekerjaans" :key="p.id"  :value="p.id">{{p.name}}</option>
          </select-input>
          <select-input v-model="form.hsl_wali" :errors="$page.errors.hsl_wali" class="pr-6 pb-8 w-full lg:w-1/2" label="Guardian's Income">
            <option v-for="p in penghasilans" :key="p.id"  :value="p.id">{{p.nilai}}</option>
          </select-input>
          <text-input v-model="form.alamat_domisili" :errors="$page.errors.alamat_domisili" class="pr-6 pb-8 w-1/2" label="Residence Address" />
          <text-input v-model="form.tgl_masuk" type="date" :errors="$page.errors.tgl_masuk" class="pr-6 pb-8 w-1/2" label="Date of Entry" />
          <text-input v-model="form.ket" :errors="$page.errors.ket" class="pr-6 pb-8 w-1/2" label="Information" />
          <text-input v-model="form.tgl_byg" type="date" :errors="$page.errors.tgl_byg" class="pr-6 pb-8 w-1/2" label="Tgl Byg" />
          <text-input v-model="form.tgl_istirahat" type="date" :errors="$page.errors.tgl_istirahat" class="pr-6 pb-8 w-1/2" label="Break Date" />
          <text-input v-model="form.tgl_pp" type="date" :errors="$page.errors.tgl_pp" class="pr-6 pb-8 w-1/2" label="Tgl PP" />

        </div>
       
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <input type="hidden" v-model="form.addAgain" :errors="$page.errors.year" class="pr-6 pb-8 w-full lg:w-1/2" />
          <loading-button ref="sbbtn" :loading="sending" class="btn-indigo" type="submit">Save {{info}}</loading-button>
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
import axios from 'axios'
import VueAxios from 'vue-axios'

export default {
  metaInfo: { title: 'User Profile' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    VueAxios,
    axios
  },
  props: {
    user: Array,
    profile: Array,
    pekerjaans: Array,
    pendidikans: Array,
    penghasilans: Array,
    kodepos: Array,
  },
  remember: 'form',
  data() {
    return {
      info: "User",
      sending: false,
      form: {
        no_induk: this.profile.no_induk,
        nisn: this.profile.nisn,
        nik: this.profile.nik,
        kps: this.profile.kps,
        kip: this.profile.kip,
        kks: this.profile.kks,
        pkh: this.profile.pkh,
        no_kk: this.profile.no_kk,
        // prov: this.profile.prov,
        // kec: this.profile.kec,
        nm_ayah: this.profile.nm_ayah,
        ktp_ayah: this.profile.ktp_ayah,
        pend_ayah: this.profile.pend_ayah,
        krj_ayah: this.profile.krj_ayah,
        nm_ibu: this.profile.nm_ibu,
        ktp_ibu: this.profile.ktp_ibu,
        pend_ibu: this.profile.pend_ibu,
        krj_ibu: this.profile.krj_ibu,
        hsl_wali: this.profile.hsl_wali,
        alamat_domisili: this.profile.alamat_domisili,
        tgl_masuk: this.profile.tgl_masuk,
        ket: this.profile.ket,
        tgl_byg: this.profile.tgl_byg,
        tgl_istirahat: this.profile.tgl_istirahat,
        tgl_pp: this.profile.tgl_pp,
        prov:parseInt(this.profile.prov),
      },
      arKodepos: this.kodepos,
    }
  },
  methods: {
    fetchOptions(search, loading){
      if(search != "")
      {
        loading(true)
        axios.get(route("kodepos") + "?search="+search).then((response) => {
          this.arKodepos = response.data
          loading(false)
        })
      }
    },
    submit() {
      this.sending = true
      this.$inertia.post(this.route('users.profiles', this.user.id), this.form)
        .then(() => 
        {
          this.sending = false
          this.form.name = null
          this.form.icon = ''
          this.form.route = null
          this.form.parentId = 0
          this.form.order_menu = null
          this.form.description = null
        })
    },
  },
}
</script>
