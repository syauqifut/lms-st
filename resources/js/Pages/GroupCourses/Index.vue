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
      <inertia-link class="btn-indigo" :href="route('group_courses.create')">
        <span>Buat</span>
        <span class="hidden md:inline">{{menuName}}</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">No</th>
          <th class="px-6 pt-6 pb-4">Course</th>
          <th class="px-6 pt-6 pb-4">Group</th>
        </tr>
        <tr
          v-for="GroupCourse in GroupCourses.data"
          :key="GroupCourse.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
         
           <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('group_courses.edit', GroupCourse.id)"
            >
              {{ GroupCourse.id }}
             
            </inertia-link>
          </td>
           <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('group_courses.edit', GroupCourse.id)"
              tabindex="-1"
            >{{ GroupCourse.course.title }}</inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('group_courses.edit', GroupCourse.id)"
              tabindex="-1"
            >{{ GroupCourse.group.classes }}</inertia-link>
          </td>
         
        </tr>
        <tr v-if="GroupCourses.data.length === 0">
          <td class="border-t px-6 py-4" colspan="5">Tidak ada Data.</td>
        </tr>
      </table>
    </div>
    <pagination :links="GroupCourses.links" />
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
  metaInfo: { title: "Grup Courses" },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    GroupCourses: Object,
    filters: Object,
    menuName: String,
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
            "group_courses",
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
