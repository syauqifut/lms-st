<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl"><inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('courses.index')">Learning / </inertia-link> Learning Users</h1>
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
        <inertia-link v-if="this.$page.auth.user.usertype_id == 3 || this.$page.auth.user.usertype_id == 1" class="btn-indigo" :href="route('list_approve_course_student')">
          Approve Join
        </inertia-link>
        <inertia-link v-else class="btn-indigo" :href="route('courses.create')">
          <span>Create</span>
          <span class="hidden md:inline">Learning</span>
        </inertia-link>
        
      </div>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Title</th>
          <!-- <th class="px-6 pt-6 pb-4">Deskripsi</th> -->
          <th class="px-6 pt-6 pb-4">Code Join</th>
          <th class="px-6 pt-6 pb-4">Subject</th>
          <th class="px-6 pt-6 pb-4">Category</th>
          <th class="px-6 pt-6 pb-4">Level</th>
          <th class="px-6 pt-6 pb-4">Teacher</th>
          <th class="px-6 pt-6 pb-4">Group</th>
          <th class="px-6 pt-6 pb-4">Status</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr
          v-for="course in courses.data"
          :key="course.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('course_users_show', course.id)">
              {{ course.title }}
            </inertia-link>
          </td>
          <td class="border-t">
             <div class="relative hover-trigger w-full" v-clipboard="course.join_code" v-clipboard:success="clipboardSuccessHandler">
              
              <span :id="course.join_code">
                {{ course.join_code }}
               </span>
            
               <i class="fa fa-copy fa-lg" />
              </div>
          </td>

          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course_users_show', course.id)"
              tabindex="-1"
            >
              {{ course.subject ? course.subject.name : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course_users_show', course.id)"
              tabindex="-1"
            >
              {{ course.category ? course.category.title : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course_users_show', course.id)"
              tabindex="-1"
            >
              {{ course.level ? course.level.title : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course_users_show', course.id)"
              tabindex="-1"
            >
              {{ course.teacher ? course.teacher.fullname : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course_users_show', course.id)"
              tabindex="-1"
            >
               {{ course.group ? course.group.classes+"-"+course.group.huruf+"("+course.group.year+")" : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course_users_show', course.id)"
              tabindex="-1"
            >
              {{ course.is_active ? 'Active' : 'Not Active' }}
            </inertia-link>
          </td>
          <td class="border-t">
            
          </td>

          
        </tr>
        <tr v-if="courses.data.length === 0">
          <td class="border-t px-7 py-4 text-center" colspan="7">No Learning data.</td>
        </tr>
      </table>
    </div>
    <pagination :links="courses.links" />
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
  metaInfo: { title: 'Learning Users' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    courses: Object,
    filters: Object,
  },
  data() {
    return {
      img_src: '/images/courses/',
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
            'courses.index',
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
     clipboardSuccessHandler ({ value, event }) {
      alert('Success copy code ' + value);
    },
  },
}
</script>
