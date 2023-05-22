<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('users')">Users</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create {{this.role ?this.role.name: ''}}
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.first_name" :errors="$page.errors.first_name" class="pr-6 pb-8 w-full lg:w-1/2" label="First name" />
          <text-input v-model="form.last_name" :errors="$page.errors.last_name" class="pr-6 pb-8 w-full lg:w-1/2" label="Last name" />
          <text-input v-model="form.username" :errors="$page.errors.username" class="pr-6 pb-8 w-full lg:w-1/2" label="Username" />
          <text-input v-model="form.email" :errors="$page.errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.password" :errors="$page.errors.password" class="pr-6 pb-8 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Password" />
          <file-input v-model="form.photo_path" :errors="$page.errors.photo_path" class="pr-6 pb-8 w-full lg:w-1/2" type="file" accept="image/*" label="Photo" />
          <text-input v-model="form.adress" :errors="$page.errors.adress" class="pr-6 pb-8 w-full lg:w-1/2" label="Adress" />
          <text-input v-model="form.mobilephone" type="number" :errors="$page.errors.mobilephone" class="pr-6 pb-8 w-full lg:w-1/2" label="Mobile Phone" />
          <text-input v-model="form.city" :errors="$page.errors.city" class="pr-6 pb-8 w-full lg:w-full" label="City" />
          <div class="pr-6 pb-8 w-full lg:w-1/2">
            <h1 class="mb-1">Country</h1>
            <v-select label="name" :reduce="country => country.name" v-model="form.country" :options="options"
              ></v-select>
          </div>
          <!-- <text-input v-model="form.country" :errors="$page.errors.country" class="pr-6 pb-8 w-full lg:w-1/2" label="Country" /> -->
          <text-input v-model="form.birthplace" :errors="$page.errors.birthplace" class="pr-6 pb-8 w-full lg:w-1/2" label="Birth Place" />
          <text-input v-model="form.birthdate" type="date" :errors="$page.errors.birthdate" class="pr-6 pb-8 w-full lg:w-1/2" label="Birth Date" />
          <select-input v-model="form.gender" :errors="$page.errors.gender" class="pr-6 pb-8 w-full lg:w-1/2" label="Gender">
            <option :value="null"></option>
            <option value="P">Male</option>
            <option value="L">Female</option>
          </select-input>
          <select-input v-model="form.usertype_id" :errors="$page.errors.usertype_id" class="pr-6 pb-8 w-full lg:w-1/2" label="User Type">
            <option :value="null"></option>
            <option v-for="t in listTypes" :key="t.id" :value="t.id">{{t.name}}</option>
          </select-input>
<!--           
          <select-input v-if="this.role.id=='2'" v-model="form.parent_id" :errors="$page.errors.parent_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Parent">
            <option :value="null">--NO PARENT--</option>
            <option v-for="u in listUsers" :key="u.id" :value="u.id">{{u.first_name + " " + u.last_name}}</option>
          </select-input> -->

          <div class="pr-6 pb-8 w-full lg:w-1/2" v-if="this.role.id=='2'">
            <h1 class="mb-1">Parent</h1>
            <v-select label="fullname" :reduce="listUsers => listUsers.id" v-model="form.parent_id" :options="listUsers"></v-select>
          </div>

          
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button :loading="sending" class="btn-indigo" type="submit">Create User</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import FileInput from '@/Shared/FileInput'
import jsonCountry from "@/Shared/ListCountries.json";
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Create User' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
  },
  props: {
    listUsers: Array,
    listTypes: Array,
    role: Object,
    representative: Number,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        
        first_name: null,
        last_name: null,
        email: null,
        password: null,
        // owner: false,
        photo_path: null,
        username:null,
        adress:null,
        gender:null,
        country:"Indonesia",
        mobilephone:null,
        birthplace:null,
        birthdate:null,
        usertype_id: this.role?this.role.id:null,
        representative: this.role_id === "5"? this.representative : null,
        parent_id:this.role_id !== "5"? this.representative : null,
        city:null,
      },
      arKodepos: this.kodepos,
      options: jsonCountry
    }
  },
  methods: {
    submit() {
      this.sending = true
      var data = new FormData()
      data.append('first_name', this.form.first_name || '')
      data.append('last_name', this.form.last_name || '')
      data.append('email', this.form.email || '')
      data.append('password', this.form.password || '')
      data.append('username', this.form.username || '')
      data.append('adress', this.form.adress || '')
      data.append('city', this.form.city || '')
      data.append('gender', this.form.gender || '')
      data.append('photo_path', this.form.photo_path || '')
      data.append('country', this.form.country || '')
      data.append('mobilephone', this.form.mobilephone || '')
      data.append('birthplace', this.form.birthplace || '')
      data.append('birthdate', this.form.birthdate || '')
      data.append('usertype_id', this.form.usertype_id || '')
      data.append('representative', this.form.representative || '')
      data.append('parent_id', this.form.parent_id || '')
      this.$inertia.post(this.route('users.store'), data)
        .then(() => this.sending = false)
    },
    isNumber: function(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
        evt.preventDefault();;
      } else {
        return true;
      }
    }
  
  },
}
</script>
