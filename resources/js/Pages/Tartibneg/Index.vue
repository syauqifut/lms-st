<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Tata Tertib and Violation</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <a :href="route('import-tartibs')" class="btn-indigo">Upload Tata Tertib</a>
      <inertia-link class="btn-indigo" :href="route('tartibs.create')">
        <span>Create</span>
        <span class="hidden md:inline">Tartib</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Code Violation</th>
          <th class="px-6 pt-6 pb-4">Name Viiolation</th>
          <th class="px-6 pt-6 pb-4">Type</th>
          <th class="px-6 pt-6 pb-4">Category</th>
          <th class="px-6 pt-6 pb-4">Score</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Status</th>
        </tr>
        <tr v-for="tartib in tartibs.data" :key="tartib.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('tartibs.edit', tartib.id)">
              {{ tartib.kode_pelanggaran }}
              <icon v-if="tartib.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tartibs.edit', tartib.id)" tabindex="-1">
              {{ tartib.nama_pelanggaran }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tartibs.edit', tartib.id)" tabindex="-1">
              {{ tartib.jenis }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tartibs.edit', tartib.id)" tabindex="-1">
              {{ tartib.kategori }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tartibs.edit', tartib.id)" tabindex="-1">
              {{ tartib.skor }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('tartibs.edit', tartib.id)" tabindex="-1">
              {{ tartib.is_active ? 'Aktif' : 'Tidak Aktif' }}
            </inertia-link>
          </td>
         
        </tr>
        <tr v-if="tartibs.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No tartib data.</td>
        </tr>
      </table>
    </div>
    <pagination :links="tartibs.links" />
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
  metaInfo: { title: 'Tartibs' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    tartibs: Object,
    filters: Object,
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
        this.$inertia.replace(this.route('tartibs', Object.keys(query).length ? query : { remember: 'forget' }))
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
