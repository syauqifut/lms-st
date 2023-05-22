<template>
    <div class="flex flex-wrap">
    <div class="w-full">
      <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row ">
        <li v-for="type in types" :key="type.id" class="-mb-px  last:mr-0 flex-auto text-center">
          <a class="text-xs font-bold uppercase px-5 py-3 shadow-lg rounded block leading-normal" v-on:click="toggleTabs(type.id)" v-bind:class="{'text-indigo-600 bg-white': openTab !== type.id, 'text-white bg-indigo-600': openTab === type.id}">
            {{type.name}}
          </a>
        </li>
      </ul>
      <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
        <div class="px-4 py-5 flex-auto">
          <div class="tab-content tab-space">
            <div v-for="type in types" :key="type.id" v-bind:class="{'hidden': openTab !== type.id, 'block': openTab === type.id}">
              <div>
                <h1 class="mb-8 font-bold text-3xl">User {{type.name}}</h1>
                 <div class="mb-6 flex justify-between items-center">
      
       <div class="mb-6 flex justify-between items-center">
      <a class="btn-indigo" :href="route('users.exportexcell',type.id)" target="_blank">
        <span>Export Excel</span>
      </a>
    </div>
    </div>
                <div class="mb-6 flex justify-between items-center">
                  <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                    <label class="block text-gray-700">Role:</label>
                    <select v-model="form.role" class="mt-1 w-full form-select">
                      <option :value="null" />
                      <option value="user">User</option>
                      <option value="owner">Owner</option>
                    </select>
                    <label class="mt-4 block text-gray-700">Trashed:</label>
                    <select v-model="form.trashed" class="mt-1 w-full form-select">
                      <option :value="null" />
                      <option value="with">With Trashed</option>
                      <option value="only">Only Trashed</option>
                    </select>
                  </search-filter>
                  <inertia-link class="btn-indigo" :href="route('users.create', type.id)">
                    <span>Create</span>
                    <span class="hidden md:inline">{{type.name}}</span>
                  </inertia-link>
                </div>
                <div class="bg-white rounded shadow overflow-x-auto">
                  <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-bold">
                      <th class="px-6 pt-6 pb-4">Name</th>
                      <th class="px-6 pt-6 pb-4">Email</th>
                      <th class="px-6 pt-6 pb-4" colspan="2">Role</th>
                    </tr>
                    <tr v-for="user in type.users" :key="user.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                      <td class="border-t">
                        <div v-if="type.id == 1 && !form.is_admin">
                          <div class="px-6 py-4 flex items-center focus:text-indigo-500">
                            <img v-if="user.photo" class="block w-5 h-5 rounded-full mr-2 -my-2" :src="user.photo">
                            {{ user.name }}
                            <icon v-if="user.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
                          </div>
                        </div>
                        <div v-else-if="type.id == 1 && form.is_admin">
                          <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('users.edit', user.id)">
                            <img v-if="user.photo" class="block w-5 h-5 rounded-full mr-2 -my-2" :src="user.photo">
                            {{ user.name }}
                            <icon v-if="user.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
                          </inertia-link>
                        </div>
                        <div v-else>
                          <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('users.edit', user.id)">
                            <img v-if="user.photo" class="block w-5 h-5 rounded-full mr-2 -my-2" :src="user.photo">
                            {{ user.name }}
                            <icon v-if="user.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
                          </inertia-link>
                        </div>
                      </td>
                      <td class="border-t">
                        <div v-if="type.id == 1 && !form.is_admin">
                          <div class="px-6 py-4 flex items-center">
                            {{ user.email }}
                          </div>
                        </div>
                        <div v-else-if="type.id == 1 && form.is_admin">
                          <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
                            {{ user.email }}
                          </inertia-link>
                        </div>
                        <div v-else>
                          <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
                            {{ user.email }}
                          </inertia-link>
                        </div>
                      </td>
                      <td class="border-t">
                        <div v-if="type.id == 1 && !form.is_admin">
                          <div class="px-6 py-4 flex items-center">
                            {{ user.owner ? 'Owner' : 'User' }}
                          </div>
                        </div>
                        <div v-else-if="type.id == 1 && form.is_admin">
                          <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
                            {{ user.owner ? 'Owner' : 'User' }}
                          </inertia-link>
                        </div>
                        <div v-else>
                          <inertia-link class="px-6 py-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
                            {{ user.owner ? 'Owner' : 'User' }}
                          </inertia-link>
                        </div>
                      </td>
                      <td class="border-t w-px">
                        <div v-if="type.id == 1 && !form.is_admin">
                          <div class="px-4 flex items-center">
                            <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
                          </div>
                        </div>
                        <div v-else-if="type.id == 1 && form.is_admin">
                          <inertia-link class="px-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
                            <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
                          </inertia-link>
                        </div>
                        <div v-else>
                          <inertia-link class="px-4 flex items-center" :href="route('users.edit', user.id)" tabindex="-1">
                            <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
                          </inertia-link>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="users.length === 0">
                      <td class="border-t px-6 py-4" colspan="4">No users found.</td>
                    </tr>
                  </table>
                </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Users' },
  layout: Layout,
  components: {
    Icon,
    SearchFilter,
  },
  props: {
    users: Array,
    filters: Object,
    types: Array,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        role: this.filters.role,
        trashed: this.filters.trashed,
        is_admin: this.$page.auth.user.usertype_id == 1,
      },
      openTab: 1
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('users', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    toggleTabs: function(tabNumber){
      this.openTab = tabNumber
    }
  },
}
</script>
