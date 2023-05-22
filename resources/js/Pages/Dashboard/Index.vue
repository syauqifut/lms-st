<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Dashboard</h1>
    <!-- <p class="mb-8 leading-normal">Hey there! Welcome to Ping CRM, a demo app designed to help illustrate how <a class="text-indigo-500 underline hover:text-orange-600" href="https://inertiajs.com">Inertia.js</a> works.</p>
    <div class="mb-8 flex">
      <inertia-link class="btn-indigo" href="/500">500 error</inertia-link>
      <inertia-link class="btn-indigo ml-1" href="/404">404 error</inertia-link>
    </div>
    <p class="leading-normal">👆 These links are intended to be broken to illustrate how error handling works with Inertia.js.</p> -->


    <div class="bg-white rounded shadow overflow-x-auto" >  
      <h1 class="mb-8 font-bold text-3xl">&nbsp;&nbsp;Schedule Today</h1>
      <table width="950px" class="no-wrap">
        <tr class="text-left font-bold">
           <th v-if="$page.auth.user.usertype_id==5" class="px-6 pt-6 pb-4">Name</th>
          <th v-if="$page.auth.user.usertype_id==4||$page.auth.user.usertype_id==1" class="px-6 pt-6 pb-4">Lecture Name</th>
          <th class="px-6 pt-6 pb-4">Lesson</th>
          <th class="px-6 pt-6 pb-4">Lesson Start</th>
          <th class="px-6 pt-6 pb-4">Lesson End</th>
          <th v-if="$page.auth.user.usertype_id!=5" class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr v-for="courseModules in courseModules.data" :key="courseModules.course_module_id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td v-if="$page.auth.user.usertype_id==5" class="border-t">
             {{ courseModules.student }}
          </td>

          <td v-if="$page.auth.user.usertype_id==4||$page.auth.user.usertype_id==1" class="border-t">
              {{ courseModules.guru }} 
          </td>

          <td class="border-t">&nbsp;
              {{ courseModules.title }}
          </td>
          <td class="border-t">
              {{ courseModules.start }}
          </td>
          <td class="border-t">
              {{ courseModules.end }}
          </td>
          <td  v-if="$page.auth.user.usertype_id!=5">
          <a :href="route('detail.course.modules', courseModules.course_module_id)" data-toggle="tooltip" title="Jadwal" class="flex-1 mx-1">
                      <span class="inline-block bg-yellow-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2"> <i class="fa fa-calendar" aria-hidden="true"></i> Jadwal</span>
                    </a>
          </td>
        </tr>
      </table>
    </div>
    <br><br>
    <div class="bg-white rounded shadow overflow-x-auto">
      <h1 class="mb-8 font-bold text-3xl">&nbsp;&nbsp;Task</h1>
      <!-- STUDENT -->
      <table width="98%" v-if="$page.auth.user.usertype_id==2" class="no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Lesson</th>
          <th class="px-2 pt-2 pb-3">Task Name</th>
          <th class="px-6 pt-6 pb-4">Deadline</th>
          <th class="px-6 pt-6 pb-4">Status</th>
          <th class="px-6 pt-6 pb-4">Score</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr v-for="courseTasks in courseTasks.data" :key="courseTasks.task_id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
              {{ courseTasks.title }}
          </td>
           <td class="border-t">
              {{ courseTasks.task }}
          </td>
          <td class="border-t">
              {{ courseTasks.end }}
          </td>
          <td class="border-t">
              {{ courseTasks.status }}
          </td>
          <td class="border-t">
              {{ courseTasks.mark }}
          </td>
          <td>
          <a :href="route('tasks.index', courseTasks.course_module_id)" data-toggle="tooltip" title="Task" class="flex-1 mx-1">
                      <span class="inline-block bg-yellow-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2"> <i class="fa fa-question" aria-hidden="true"></i> Task</span>
                    </a>
          </td>
        </tr>
        <!-- <tr v-if="courseModules.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">Tidak ada subject.</td>
        </tr> -->
      </table>

      <!-- GURU -->
      <table width="98%" v-if="$page.auth.user.usertype_id==3 || $page.auth.user.usertype_id==1 || $page.auth.user.usertype_id==4" class="no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Unmarked Task</th>
          <th class="px-2 pt-2 pb-3">Task Name</th>
          <th class="px-6 pt-6 pb-4">Deadline</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr v-for="courseTasks in courseTasks.data" :key="courseTasks.task_id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
              {{ courseTasks.total }}
          </td>
           <td class="border-t">
              {{ courseTasks.title }}
          </td>
          <td class="border-t">
              {{ courseTasks.end }}
          </td>
          <td>
              <a :href="route('tasks.get_student_upload', courseTasks.task_id)" data-toggle="tooltip" title="Task" class="flex-1 mx-1">
                      <span class="inline-block bg-yellow-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2"> <i class="fa fa-question" aria-hidden="true"></i> Task</span>
              </a>
          </td>
        </tr>
        <!-- <tr v-if="courseModules.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">Tidak ada subject.</td>
        </tr> -->
      </table>

      <!-- ORANG TUA -->
      <table width="98%" v-if="$page.auth.user.usertype_id==5" class="no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Students</th>
          <th class="px-6 pt-6 pb-4">Lesson</th>
          <th class="px-2 pt-2 pb-3">Task Name</th>
          <th class="px-6 pt-6 pb-4">Deadline</th>
          <!-- <th class="px-6 pt-6 pb-4">Stat</th> -->
          <th class="px-6 pt-6 pb-4">Status</th>
          <th class="px-6 pt-6 pb-4">Score</th>
          <!-- <th class="px-6 pt-6 pb-4">Aksi</th> -->
        </tr>
        <tr v-for="courseTasks in courseTasks.data" :key="courseTasks.task_id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
              {{ courseTasks.student }}
          </td>
          <td class="border-t">
              {{ courseTasks.title }}
          </td>
           <td class="border-t">
              {{ courseTasks.task }}
          </td>
          <td class="border-t">
              {{ courseTasks.end }}
          </td>
          <!-- <td class="border-t">
              {{ courseTasks.stat }}
          </td> -->
          <td class="border-t">
              {{ courseTasks.status }}
          </td>
          <td class="border-t">
              {{ courseTasks.mark }}
          </td>
          <!-- <td>
          <a :href="route('tasks.index', courseTasks.course_module_id)" data-toggle="tooltip" title="Task" class="flex-1 mx-1">
                      <span class="inline-block bg-yellow-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2"> <i class="fa fa-question" aria-hidden="true"></i> Task</span>
                    </a>
          </td> -->
        </tr>
        <!-- <tr v-if="courseModules.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">Tidak ada subject.</td>
        </tr> -->
      </table>
    </div> </br></br>

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
  metaInfo: { title: 'Dashboard' },
  layout: Layout,
  props: {
    courseModules: Object,
    courseTasks: Object,
    // filters: Object,
  },
}
</script>
