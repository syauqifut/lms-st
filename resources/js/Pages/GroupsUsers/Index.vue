<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">{{menuName}}</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('list-approve-student')">
        <span>Approve Join</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">ID</th>
          <th class="px-6 pt-6 pb-4">Groups</th>
          <th class="px-6 pt-6 pb-4">Year</th>
          <th class="px-6 pt-6 pb-4">Code Group</th>
          <th class="px-6 pt-6 pb-4">Semester</th>
          <th class="px-6 pt-6 pb-4">Main Teacher</th>
          <!-- <th class="px-6 pt-6 pb-4" colspan="2">Phone</th> -->
        </tr>
        <tr v-for="group in groups.data" :key="group.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('group_users.show', group.id)">
              {{ group.id }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('group_users.show', group.id)">
              {{ group.classes }}
              <icon v-if="group.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.year }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.code }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              {{ group.academicterms === 1 ? 'Ganjil' : "Genap" }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('group_users.show', group.id)" tabindex="-1">
              <div v-if="group.mainteacherr">
                {{ group.mainteacherr.fullname }}
              </div>
              <div v-else>
                ksong
              </div>
            </inertia-link>
          </td>
        </tr>
        <tr v-if="groups.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No organizations found.</td>
        </tr>
      </table>
    </div>
    <pagination :links="groups.links" />
    
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
  metaInfo: { title: 'Groups' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    groups: Object,
    filters: Object,
    menuName: String,
  },
  data() {
    return {
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
        this.$inertia.replace(this.route('group_users', Object.keys(query).length ? query : { remember: 'forget' }))
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
