<template>
  <div>
    <div class="mb-8 flex justify-start max-w-3xl">
      <h1 class="font-bold text-3xl">
        <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('users')">Users</inertia-link>
        <span class="text-indigo-400 font-medium">/</span>
        {{ form.first_name }} {{ form.last_name }}
      </h1>
      <img v-if="user.photo_path" class="block w-8 h-8 rounded-full ml-4" :src="user.photo_path">
    </div>
    <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore">
      This user has been deleted.
    </trashed-message>

    <div class="mb-8">
      <inertia-link class="btn-indigo" :href="route('users.profiles', user.id)">Profile Details</inertia-link>
      <inertia-link class="btn-indigo" :href="route('certificates.index', user.id)">Files</inertia-link>
    </div>
    <div class="lg:flex">
      <div class="order-last flex-1 bg-white rounded shadow overflow-hidden w-full max-w-3xl">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
        <div class="container mx-auto pr-6 pb-6">
          <div class="flex flex-row flex-wrap -mx-2">
            <div class="w-full h-32 md:h-64 mb-4 sm:mb-0 px-2">
              <a class="block w-full h-full bg-grey-dark bg-no-repeat bg-center bg-cover" href="#" title="Link" :style="'background-image: url('+ user.photo_path+');'">
              </a>
            </div>
          </div>
        </div>
        </div>
      </div>
      <div class=" flex-1 bg-white rounded shadow overflow-hidden max-w-3xl">
        <form @submit.prevent="submit">
          <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
            <text-input v-if="$page.auth.user.usertype_id==1||$page.auth.user.usertype_id==4" v-model="form.first_name" :errors="$page.errors.first_name" class="pr-6 pb-8 w-full lg:w-1/2" label="First name" />
            <text-input v-if="$page.auth.user.usertype_id==1||$page.auth.user.usertype_id==4" v-model="form.last_name" :errors="$page.errors.last_name" class="pr-6 pb-8 w-full lg:w-1/2" label="Last name" />
            <text-input  v-if="$page.auth.user.usertype_id==1||$page.auth.user.usertype_id==4" v-model="form.username" :errors="$page.errors.username" class="pr-6 pb-8 w-full lg:w-1/2" label="Username" />
            <text-input v-model="form.email" :errors="$page.errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Email" />
            <text-input v-model="form.password" :errors="$page.errors.password" class="pr-6 pb-8 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Password" />
            <file-input v-model="form.photo_path" :errors="$page.errors.photo_path" class="pr-6 pb-8 w-full lg:w-1/2" type="file" accept="image/*" label="Photo" />

            <text-input v-model="form.adress" :errors="$page.errors.adress" class="pr-6 pb-8 w-full lg:w-1/2" label="Adress" />
            <text-input v-model="form.city" :errors="$page.errors.city" class="pr-6 pb-8 w-full lg:w-1/2" label="City" />

            <div class="pr-6 pb-8 w-full lg:w-1/2">
              <h1 class="mb-1">Country</h1>
              <v-select label="name" :reduce="country => country.name" v-model="form.country" :options="options"
                ></v-select>
            </div>
            <!-- <text-input v-model="form.country" :errors="$page.errors.country" class="pr-6 pb-8 w-full lg:w-1/2" label="Country" /> -->
            <text-input v-model="form.mobilephone" type="number" :errors="$page.errors.mobilephone" class="pr-6 pb-8 w-full lg:w-1/2" label="Mobile Phone" />
            <text-input v-model="form.birthplace" :errors="$page.errors.birthplace" class="pr-6 pb-8 w-full lg:w-1/2" label="Birth Place" />
            <text-input v-model="form.birthdate" type="date" :errors="$page.errors.birthdate" class="pr-6 pb-8 w-full lg:w-1/2" label="Birth Date" />
            <select-input v-model="form.gender" :errors="$page.errors.gender" class="pr-6 pb-8 w-full lg:w-1/2" label="Gender">
              <option :value="null"></option>
              <option value="P">Male</option>
              <option value="L">Female</option>
            </select-input>
            <select-input v-model="form.usertype_id" :disabled="!form.is_admin" :errors="$page.errors.usertype_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Tipe User">
              <option :value="null"></option>
              <option v-for="t in listTypes" :key="t.id" :value="t.id">{{t.name}}</option>
            </select-input>
           <div class="pr-6 pb-8 w-full lg:w-1/2" >
            <h1 class="mb-1">Parent</h1>
            <v-select v-if="user.usertype_id=='2'" :disabled="!form.is_admin" label="fullname" :reduce="listUsers => listUsers.id" v-model="form.parent_id" :options="listUsers"></v-select>
          </div>


          </div>
          <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center justify-between">
            <button v-if="!user.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete User</button>
            <div class="flex items-center">
              <inertia-link v-if="form.usertype_id === 5" class="btn-indigo mr-3" :href="route('users.create',[2, user.id])">Add Anak</inertia-link>
              <inertia-link v-else-if="form.usertype_id === 2 && form.parent_id === null" class="btn-indigo mr-3" :href="route('users.create',[5, user.id])">Add Parent</inertia-link>
              <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update User</loading-button>
            </div>
          </div>
        </form>
      </div>

    </div>
    &nbsp;&nbsp;
    &nbsp;&nbsp;
      <div v-if="user.usertype_id!='2'">
        <h1 class="mb-8 font-bold text-3xl">Kids</h1>
        <div class="bg-white rounded shadow overflow-x-auto">
          <table class="w-full whitespace-no-wrap">
            <tr class="text-left font-bold">
              <th class="px-6 pt-6 pb-4">Name</th>
              <th class="px-6 pt-6 pb-4">Email</th>
              <th class="px-6 pt-6 pb-4" colspan="2">Status</th>
            </tr>
            <tr
              v-for="userC in userChild"
              :key="userC.id"
              class="hover:bg-gray-100 focus-within:bg-gray-100"
            >
                <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('users.edit', userC.id)">
                              <img v-if="userC.photo" class="block w-5 h-5 rounded-full mr-2 -my-2" :src="userC.photo">
                              {{ userC.fullname }}
                              <icon v-if="userC.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
                            </inertia-link>
                          </td>
                          <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', userC.id)" tabindex="-1">
                              {{ userC.email }}
                            </inertia-link>
                          </td>
                          <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', userC.id)" tabindex="-1">
                              {{ userC.owner ? 'Owner' : 'User' }}
                            </inertia-link>
                          </td>
                          <td class="border-t w-px">
                            <inertia-link class="px-4 flex items-center" :href="route('users.edit', userC.id)" tabindex="-1">
                              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
                            </inertia-link>
                          </td>
            </tr>
            <tr v-if="userChild.length === 0">
              <td class="border-t px-6 py-4" colspan="4">Doesn't Have Kids.</td>
            </tr>
          </table>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center justify-between">
        </div>
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
import TrashedMessage from '@/Shared/TrashedMessage'
import jsonCountry from "@/Shared/ListCountries.json";

export default {
  metaInfo() {
    return {
      title: `${this.form.first_name} ${this.form.last_name}`,
    }
  },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
    TrashedMessage,
  },
  props: {
    user: Object,
    listUsers: Array,
    listTypes: Array,
    userChild: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: null,
        // owner: false,
        photo_path: null,
        username:this.user.username,
        adress:this.user.adress,
        gender:this.user.gender,
        city:(this.user.city),
        country:this.user.country,
        mobilephone:this.user.mobilephone,
        birthplace:this.user.birthplace,
        birthdate:this.user.birthdate,
        usertype_id:this.user.usertype_id,
        parent_id:this.user.parent_id,
        parent: this.user.parent ? this.user.parent.name : '-- NO PARENT --',
        is_admin: this.$page.auth.user.usertype_id == 1,
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
      data.append('photo_path', this.form.photo_path || '')
      data.append('gender', this.form.gender || '')
      data.append('country', this.form.country || '')
      data.append('mobilephone', this.form.mobilephone || '')
      data.append('birthplace', this.form.birthplace || '')
      data.append('birthdate', this.form.birthdate || '')
      data.append('usertype_id', this.form.usertype_id || '')
      data.append('parent_id', this.form.parent_id || '')
      data.append('_method', 'put')

      this.$inertia.post(this.route('users.update', this.user.id), data)
        .then(() => {
          this.sending = false
          if (Object.keys(this.$page.errors).length === 0) {
            this.form.photo_path = null
            this.form.password = null
          }
        })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this user?')) {
        this.$inertia.delete(this.route('users.destroy', this.user.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this user?')) {
        this.$inertia.put(this.route('users.restore', this.user.id))
      }
    },
  },
}
</script>
