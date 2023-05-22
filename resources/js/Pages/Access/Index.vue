<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">{{info}}</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route(routes+'.create')">
        <span>Create</span>
        <span class="hidden md:inline">{{info}}</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">ID</th>
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Icon</th>
          <th class="px-6 pt-6 pb-4">Route</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr v-for="menu in menus.data" :key="menu.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t px-6 py-4">
              {{ menu.id }}
          </td>
          <td class="border-t px-6 py-4">
              {{ menu.name }}
          </td>
          <td class="border-t px-6 py-4">
              <i :class="menu.logo" style="font-size:30px;"></i>
          </td>
          <td class="border-t px-6 py-4">
              {{ menu.route }}
          </td>
          <td class="border-t ">
            <div class="flex">
              <inertia-link data-toggle="tooltip" title="Modules" class="btn-indigo mr-2 " :href="route(routes+'.edit', menu.id)" tabindex="-1">
                <i class="fa fa-pencil" aria-hidden="true" />
              </inertia-link>
              <inertia-link data-toggle="tooltip" title="Modules" class="btn-indigo mr-2 " :href="route(routes+'.user', menu.id)" tabindex="-1">
                <i class="fa fa-book" aria-hidden="true" />
              </inertia-link>
            </div>
          </td>
        </tr>
        <tr v-if="menus.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No menus found.</td>
        </tr>
      </table>
    </div>
    <pagination :links="menus.links" />
    
  </div>

  
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Menus' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    menus: Object,
    filters: Object,
  },
  data() {
    return {
      info: 'Access',
      routes: 'access',
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('organizations', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
