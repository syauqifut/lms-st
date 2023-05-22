<template>
  <div>
    <div class="mb-8 flex justify-start max-w-3xl">
      <h1 class="font-bold text-3xl">
        Change Password
      </h1>
      <!-- <img v-if="user.photo_path" class="block w-8 h-8 rounded-full ml-4" :src="user.photo_path">
      <img v-else class="block w-8 h-8 rounded-full ml-4" :src="ss" alt="profile picture"> -->
    </div>
    <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore">
      This user has been deleted.
    </trashed-message>

    <div class="lg:flex">
      <div class=" flex-1 bg-white rounded shadow overflow-hidden max-w-3xl mr-6">
        <form @submit.prevent="submit">
          <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
            <text-input v-model="form.password" :errors="$page.errors.password" class="pb-8 w-full" type="password" autocomplete="new-password" label="Old Password" />
            <text-input v-model="form.new" :errors="$page.errors.password" class="pb-8 w-full" type="password" autocomplete="new-password" label="New Password (min 8 char)" />
            <text-input v-model="form.new_confirmation" :errors="$page.errors.password" class="pb-8 w-full" type="password" autocomplete="new-password" label="Confirmation New Password" />
            
          </div>
          <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center justify-between">
            <div class="flex items-center">
              <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Change Password</loading-button>
            </div>
          </div>
        </form>
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
      photoPath: this.user.photo_path,
      form: {
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: null,
        new: null,
        new_confirmation: null,
        // owner: false,
        // photo_path: null,
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
      data.append('password', this.form.password || '')
      data.append('new', this.form.new || '')
      data.append('new_confirmation', this.form.new_confirmation || '')

      this.$inertia.post(this.route('users.changePassword', this.user.id), data)
        .then(() => {
          this.sending = false
          // if (Object.keys(this.$page.errors).length === 0) {
          //   this.form.photo_path = null
          //   this.form.password = null
          // }
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
