<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('rapor.index')">Generate Report</inertia-link>
    </h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">NIM</th>
          <th class="px-6 pt-6 pb-4">Names</th>
          <th class="px-6 pt-6 pb-4">Tugas</th>
          <th class="px-6 pt-6 pb-4">UTS</th>
          <th class="px-6 pt-6 pb-4">UAS</th>
          <th class="px-6 pt-6 pb-4">Perform</th>
          <th class="px-6 pt-6 pb-4">Sakit</th>
          <th class="px-6 pt-6 pb-4">Izin</th>
          <th class="px-6 pt-6 pb-4">Alpha</th>
          <th class="px-6 pt-6 pb-4">Score</th>
          <th class="px-6 pt-6 pb-4">Alphabet</th>
          <th class="px-6 pt-6 pb-4">Groups</th>
          <th class="px-6 pt-6 pb-4">Homeroom Teacher</th>
          <th class="px-6 pt-6 pb-4">Subjects</th>
          <th class="px-6 pt-6 pb-4">Teachers</th>
        </tr>
        <tr
          v-for="rapor in rapor.data"
          :key="rapor.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.nim }} 
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.nama }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.tugas }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.uts }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.uas }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.perform }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.sakit }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.izin }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.alpha }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.nilai }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.huruf }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.kelas }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.walikelas }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.subject }}
            </inertia-link>
          </td>
          <td class="border-t items-center focus:text-indigo-500">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('rapor.edit', rapor.id )">
              {{ rapor.gurupengajar }}
            </inertia-link>
          </td>

          <td class="border-t w-px">
            <inertia-link
              class="px-4 flex items-center"
              :href="route('rapor.edit', rapor.id)"
              tabindex="-1"
            >
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="rapor.data.length === 0">
          <td class="border-t px-6 py-4" colspan="5">No report data.</td>
        </tr>
      </table>
    </div>
    <pagination :links="rapor.links" />


  </div>
</template>

<script>
import Icon from "@/Shared/Icon";
import Layout from "@/Shared/Layout";
import mapValues from "lodash/mapValues";
import Pagination from "@/Shared/Pagination";
import pickBy from "lodash/pickBy";
import SearchFilter from "@/Shared/SearchFilter";
import throttle from "lodash/throttle";

export default {
  metaInfo: { title: "Cetak Rapor" },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    rapor: Object,
    filter: Object,
    group: Array,
    subject: Array,
    user: Array,
  },
  data() {
    return {
      form: {
        search: this.filter.search,
        trashed: this.filter.trashed,
      },
    };
  },
  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form);
        this.$inertia.replace(
          this.route(
            "rapor.create",
            Object.keys(query).length ? query : { remember: "forget" }
          )
        );
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
  },
};
</script>
