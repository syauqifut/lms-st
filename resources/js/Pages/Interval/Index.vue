<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Score Interval</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <div>
        <inertia-link class="btn-indigo" :href="route('interval.create')">
          <span>Create</span>
          <span class="hidden md:inline">Interval</span>
        </inertia-link>
        <inertia-link class="btn-indigo" :href="route('import-interval')">
          <span>Import Interval</span>
        </inertia-link>
      </div>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Id</th>
          <th class="px-6 pt-6 pb-4">Min Mark</th>
          <th class="px-6 pt-6 pb-4">Max Mark</th>
          <th class="px-6 pt-6 pb-4">Min Average</th>
          <th class="px-6 pt-6 pb-4">Max Average</th>
          <th class="px-6 pt-6 pb-4">Level</th>
          <th class="px-6 pt-6 pb-4">Alphabet</th>
          <th class="px-6 pt-6 pb-4">Status</th>
        </tr>
        <tr
          v-for="interval in interval.data"
          :key="interval.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('interval.edit', interval.id)"
            >
              {{ interval.id }}
            </inertia-link>
          </td>
           <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('interval.edit', interval.id)"  tabindex="-1"
            >
              {{ interval.minmark }}
            </inertia-link>
          </td>
           <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('interval.edit', interval.id)"  tabindex="-1"
            >
              {{ interval.maxmark }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('interval.edit', interval.id)"  tabindex="-1"
            >
              {{ interval.minavg }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('interval.edit', interval.id)"  tabindex="-1"
            >
              {{ interval.maxavg }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('interval.edit', interval.id)" tabindex="-1">
              {{ interval.level.title }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('interval.edit', interval.id)"  tabindex="-1"
            >
              {{ interval.alphabet }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('interval.edit', interval.id)"  tabindex="-1"
            >
              {{ interval.status }}
            </inertia-link>
          </td>

          <td class="border-t w-px">
            <inertia-link
              class="px-4 flex items-center"
              :href="route('interval.edit', interval.id)"
              tabindex="-1"
            >
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="interval.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No interval data.</td>
        </tr>
      </table>
    </div>
    <pagination :links="interval.links" />
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
  metaInfo: { title: "Interval" },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    interval: Object,
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
            "interval.index",
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
