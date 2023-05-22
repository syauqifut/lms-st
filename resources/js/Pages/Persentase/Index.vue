<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Score Percentage</h1>
    <div class="mb-6 flex justify-between items-center">
      <!-- <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter> -->
      <div>
        <inertia-link class="btn-indigo" :href="route('persentase.create')">
          <span>Create</span>
          <span class="hidden md:inline">Percentage</span>
        </inertia-link>
        <!-- <inertia-link class="btn-indigo" :href="route('import-persen')">
          <span>Import Persen</span>
        </inertia-link> -->
      </div>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <!-- <th class="px-6 pt-6 pb-4">Id</th> -->
          <th class="px-6 pt-6 pb-4">Category</th>
          <th class="px-6 pt-6 pb-4">Task Type</th>
          <th class="px-6 pt-6 pb-4">Percent</th>
        </tr>
        <tr
          v-for="persen in persen.data"
          :key="persen.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <!-- <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('persentase.edit', persen.id)"
            >
              {{ persen.id }}
            </inertia-link>
          </td> -->
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('persentase.edit', persen.id)"  tabindex="-1"
            >
              {{ persen.category.title }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('persentase.edit', persen.id)"  tabindex="-1"
            >
              {{ persen.task_type }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link 
              class="px-6 py-4 flex items-center" 
              :href="route('persentase.edit', persen.id)" tabindex="-1">
              {{ persen.persen }}
            </inertia-link>
          </td>

          <td class="border-t w-px">
            <inertia-link
              class="px-4 flex items-center"
              :href="route('persentase.edit', persen.id)"
              tabindex="-1"
            >
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="persen.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No Percentage.</td>
        </tr>
      </table>
    </div>
    <pagination :links="persen.links" />
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
  metaInfo: { title: "Persentase" },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    persen: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    };
  },
  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form);
        this.$inertia.replace(
          this.route(
            "persen.index",
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
