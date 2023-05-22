<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Certificates</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link v-if="$page.auth.user.usertype_id != 1 && $page.auth.user.usertype_id != 4" class="btn-indigo" :href="route('certificates.create', user.id)">
        <span>Tambah</span>
        <span class="hidden md:inline">Berkas</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Certificate Name</th>
          <th class="px-6 pt-6 pb-4">Information</th>
          <th class="px-6 pt-6 pb-4">Year</th>
          <th class="px-6 pt-6 pb-4">Description Certificate</th>
          <th class="px-6 pt-6 pb-4">Files</th>
        </tr>
        <tr
          v-for="certificate in certificates.data"
          :key="certificate.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('certificates.edit', certificate.id)"
            >
              {{ certificate.nama }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('certificates.edit', certificate.id)"
            >
              {{ certificate.keterangan }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('certificates.edit', certificate.id)"
            >
              {{ certificate.tahun }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('certificates.edit', certificate.id)"
            >
              {{ certificate.desc_certificate }}
            </inertia-link>
          </td>
          <td class="border-t">
            <!-- <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('certificates.edit', certificate.id)"
            > -->
            <a :href="route('certificates.download_file', certificate.id)" class="text-blue-500 mb-5 text-lg hover:underline" target="_blank">Download Certificates</a>
            <!-- </inertia-link> -->
          </td>
        </tr>
        <tr v-if="certificates.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No Files.</td>
        </tr>
      </table>
    </div>
    <pagination :links="certificates.links" />
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Berkas' },
  layout: Layout,
  components: {
    Pagination,
    SearchFilter,
  },
  props: {
    certificates: Object,
    user: Object,
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
      handler: throttle(function () {
        let query = pickBy(this.form)
        this.$inertia.replace(
          this.route(
            'certificates.index',
            Object.keys(query).length ? query : { remember: 'forget' }
          )
        )
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
