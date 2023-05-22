<template>
  <div>

    <h1 class="mb-8 font-bold text-3xl">Learning Not Active</h1>
    <!-- view mobile -->
    <div class="mb-6 flex justify-between items-center" v-if="isMobile()">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
    </div>
    <div class="mb-6 flex justify-between items-center" v-if="isMobile()">
      <inertia-link v-if="role.is_murid" class="btn-indigo" :href="route('course-users-student')">
        All Learning
      </inertia-link>
      <inertia-link v-else class="btn-indigo" :href="route('courses.create')">
        <span>Create</span>
      </inertia-link>
      <inertia-link v-if="role.is_teacher" class="btn-indigo" :href="route('list_approve_course_student')">
        Approve Join
      </inertia-link>
    </div>
    <!-- tampilan non-mobile -->
    <div class="mb-6 flex justify-between items-center" v-if="!isMobile()">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <div>
        <inertia-link v-if="role.is_murid" class="btn-indigo" :href="route('course-users-student')">
          All Learning
        </inertia-link>
        <inertia-link v-else class="btn-indigo" :href="route('courses.create')">
          <span>Create</span>
        </inertia-link>
        <inertia-link v-if="role.is_teacher" class="btn-indigo" :href="route('list_approve_course_student')">
          Approve Join
        </inertia-link>
      </div>
    </div>

<div  v-for="course in courses.data" :key="course.id" class=" lg:w-full pb-3" >
          <!-- Article -->
          <article class="overflow-hidden bg-white rounded-lg shadow-lg">
            <header class="items-center justify-between leading-tight px-4 pt-4">
              <h1 class="text-lg">
                <a class="no-underline font-bold text-gray-800 text-lg hover:underline" :href="route('course-modules.get_by_course', course.id)">
                   {{ course.title }} - {{ course.subject ? course.subject.name : '' }} ({{course.subject ? course.subject.subject_code : '' }}) - {{ course.category ? course.category.title : '' }} - {{ course.level ? course.level.title : '' }} -  {{ course.group ? course.group.classes+"-"+course.group.huruf+" ("+course.periode+" "+course.group.year+")" : '' }}
                </a>
              </h1>

              <p >


                <span v-if="course.coursedone < 1">
                <inertia-link   class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('course-modules.get_by_course', course.id)">
                  <vue-stars :name="''+course.id" :max="5" :value="Math.round(course.avgreviews?course.avgreviews:0)" :readonly="true" />
                  {{ course.avgreviews ? course.avgreviews : '0' }} ({{course.treviews}})
                </inertia-link>
                </span>

                <span v-else>

                  <inertia-link v-if="role.is_teacher"  class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('reviews.list_approve', course.id)">
                  <vue-stars :name="''+course.id" :max="5" :value="Math.round(course.avgreviews?course.avgreviews:0)" :readonly="true" />
                  {{ course.avgreviews ? course.avgreviews : '0' }} ({{course.treviews}})
                  </inertia-link>

                  <inertia-link v-else class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('reviews.create', course.id)">
                  <vue-stars :name="''+course.id" :max="5" :value="Math.round(course.avgreviews?course.avgreviews:0)" :readonly="true" />
                  {{ course.avgreviews ? course.avgreviews : '0' }} ({{course.treviews}})
                  </inertia-link>

                </span>
              </p>

              <p class="text-sm text-gray-500 mt-2 mb-3">
                <div class="relative hover-trigger w-full"  v-clipboard="course.join_code" v-clipboard:success="clipboardSuccessHandler">

                <span :id="course.join_code">
                  {{ course.join_code }}
                </span>

                <i class="fa fa-copy fa-lg"></i>
                </div>
              </p>
              <p class="text-l text-gray-800 mt-2 mb-3">
                {{ course.teacher ? course.teacher.fullname : '' }}
              </p>

            </header>
            <footer class="flex items-center justify-between leading-none px-4 pb-4">
              <div class="pt-1">
                <span v-if="!role.is_murid" class="inline-block bg-green-200 rounded-full px-3 py-1 text-sm font-normal text-gray-700 mr-2">
                    {{ course.is_active ? 'Active' : 'Not Active' }}
                </span>

                <a :href="route('course_users_show', course.id)" v-if="!role.is_murid" class="flex-1 mx-1">
                  <span class="inline-block bg-blue-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2"><i class="fa fa-users"></i> List User</span>
                </a>


                <a :href="route('courses.edit', course.id)" v-if="!role.is_murid" class="flex-1 mx-1">
                  <span class="inline-block bg-yellow-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2"><i class="fa fa-pencil-square fa-lg" /> Update Data</span>
                </a>


                <div v-else >
                  <div v-if="course.course.is_active == 1">

                    <a :href="route('course_users_show', course.id)" data-toggle="tooltip" title="Daftar Murid" class="flex-1 mx-1">
                      <span class="inline-block bg-yellow-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2"> <i class="fa fa-users" aria-hidden="true"></i> List Student</span>
                    </a>

                    <a v-on:click="toggleModal(course.id, 'Keluar')"  data-toggle="tooltip" title="Daftar Murid" class="flex-1 mx-1">
                      <span class="inline-block bg-red-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2"> <i class="fa fa-sign-out " aria-hidden="true"></i> Leave</span>
                    </a>


                  </div>

                  <div v-else-if="course.course.is_active == 0">
                    <span  class="btn-indigo bg-red-500"
                      tabindex="-1" v-on:click="toggleModal(course.id, 'Batal')">
                      <i class="fa fa-sign-out " aria-hidden="true"></i>
                    </span>
                  </div>
                </div>


              </div>
            </footer>

          </article>
          <!-- END Article -->
        </div>


    <!--
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Judul</th>
          <th class="px-6 pt-6 pb-4">Reviews</th>
          <th class="px-6 pt-6 pb-4" v-if="!role.is_murid">Kode Join</th>
          <th class="px-6 pt-6 pb-4">Subject</th>
          <th class="px-6 pt-6 pb-4">Kategori</th>
          <th class="px-6 pt-6 pb-4">Level</th>
          <th class="px-6 pt-6 pb-4">Guru</th>
          <th class="px-6 pt-6 pb-4">Group</th>
          <th class="px-6 pt-6 pb-4">Status</th>
          <th class="px-6 pt-6 pb-4 align-center">Aksi</th>
        </tr>
        <tr
          v-for="course in courses.data"
          :key="course.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('course-modules.get_by_course', course.id)">
              {{ course.title }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('course-modules.get_by_course', course.id)">
              <vue-stars :name="''+course.id" :max="5" :value="Math.round(course.avgreviews?course.avgreviews:0)" :readonly="true" />
              {{ course.avgreviews ? course.avgreviews : '0' }} ({{course.treviews}})
            </inertia-link>
          </td>
          <td class="border-t" v-if="!role.is_murid">
             <div class="relative hover-trigger w-full"  v-clipboard="course.join_code" v-clipboard:success="clipboardSuccessHandler">

              <span :id="course.join_code">
                {{ course.join_code }}
               </span>

               <i class="fa fa-copy fa-lg" />
              </div>
          </td>

          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course-modules.get_by_course', course.id)"
              tabindex="-1"
            >
              {{ course.subject ? course.subject.name : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course-modules.get_by_course', course.id)"
              tabindex="-1"
            >
              {{ course.category ? course.category.title : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course-modules.get_by_course', course.id)"
              tabindex="-1"
            >
              {{ course.level ? course.level.title : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course-modules.get_by_course', course.id)"
              tabindex="-1"
            >
              {{ course.teacher ? course.teacher.fullname : '' }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course-modules.get_by_course', course.id)"
              tabindex="-1"
            >
              {{ course.group ? course.group.classes+"-"+course.group.huruf+"("+course.group.year+")" : '' }}
            </inertia-link>
          </td>
          <td class="border-t" v-if="!role.is_murid">
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course-modules.get_by_course', course.id)"
              tabindex="-1"
            >
              {{ course.is_active ? 'Aktif' : 'Tidak Aktif' }}
            </inertia-link>
          </td>
          <td class="border-t" v-else>
            <inertia-link
              class="px-6 py-4 flex items-center"
              :href="route('course-modules.get_by_course', course.id)"
              tabindex="-1"
            >
              {{ course_user_status[course.course.is_active]}}
            </inertia-link>
          </td>

          <td class="border-t w-px">
            <div>
              <!-- <img v-if="course.photo" class="block object-contain w-full" :src="img_src + course.photo">
              <div class="flex">
                <div v-if="!role.is_murid">
                  <!-- <a :href="route('courses.edit', course.id)"  class="flex-1 mx-1">
                    <i class="fa fa-pencil-square fa-lg" />
                  </a>
                  <inertia-link data-toggle="tooltip" title="Users"
                    class="btn-indigo mr-2 "
                    :href="route('course_users_show', course.id)"
                    tabindex="-1" >
                    <i class="fa fa-users" aria-hidden="true"></i>
                  </inertia-link>
                   <inertia-link data-toggle="tooltip" title="Modules"
                      class="btn-indigo mr-2 "
                      :href="route('course-modules.get_by_course', course.id)"
                      tabindex="-1" >
                      <i class="fa fa-book" aria-hidden="true"></i>
                    </inertia-link>
                   <inertia-link data-toggle="tooltip" title="Edit"
                    class="btn-indigo mr-2 "
                    :href="route('courses.edit', course.id)"
                    tabindex="-1" >
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </inertia-link>


                </div>

                <div v-else class="flex">

                  <div v-if="course.course.is_active == 1">
                    <inertia-link data-toggle="tooltip" title="Daftar Murid"
                      class="btn-indigo mr-2"
                      :href="route('course_users_show', course.id)"
                      tabindex="-1" >
                      <i class="fa fa-users" aria-hidden="true"></i>
                    </inertia-link>
                    <inertia-link data-toggle="tooltip" title="Modules"
                      class="btn-indigo mr-2 "
                      :href="route('course-modules.get_by_course', course.id)"
                      tabindex="-1" >
                      <i class="fa fa-book" aria-hidden="true"></i>
                    </inertia-link>
                    <span  class="btn-indigo bg-red-500" tabindex="-1" v-on:click="toggleModal(course.id, 'Keluar')" data-toggle="tooltip" title="Keluar">
                      <i class="fa fa-sign-out " aria-hidden="true"></i>
                    </span>
                  </div>



                  <div v-else-if="course.course.is_active == 0">
                    <span  class="btn-indigo bg-red-500"
                      tabindex="-1" v-on:click="toggleModal(course.id, 'Batal')">
                      <i class="fa fa-sign-out " aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <tr v-if="courses.data.length === 0">
          <td class="border-t px-7 py-4 text-center" colspan="7">Tidak ada course.</td>
        </tr>
      </table>
    </div>-->
    <pagination :links="courses.links" />
    <modal-confirmation :showModal="modal.showModal" 
    :titleModal="modal.titleModal" :descModal="modal.descModal" 
    v-on:toogle="toggleModal" v-on:confirm="confirmModal"> </modal-confirmation>
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
import ModalConfirmation from '@/Shared/ModalConfirmation'
import { VueStars } from "vue-stars"

export default {
  metaInfo: { title: 'Learning Not Active' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    ModalConfirmation,
    SearchFilter,
    VueStars
  },
  props: {
    courses: Object,
    filters: Object,
  },
  data() {
    return {
      role: {
        is_murid: this.$page.auth.user.usertype_id == 2,
        is_teacher: this.$page.auth.user.usertype_id == 3 || 
        this.$page.auth.user.usertype_id == 1 || this.$page.auth.user.usertype_id == 4,
      },
      modal: {
        showModal: false,
        descModal: 'Are you sure you want to leave this Learning?',
        titleModal: "Leave Learning",
        course_id: null,
      },
      course_user_status:{
        '-2' : "Leave",
        '-1' : "Rejected",
        '0'  : "Approval",
        '1'  : "Active",
      },
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
            'courses.courses_non_active',
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
    toggleModal: function(course_id = null, desc = ""){
      this.modal.showModal = !this.modal.showModal;
      this.modal.course_id = course_id;
      this.modal.descModal = 'Are you sure want '+ desc + ' in this learning?'
      this.modal.titleModal= desc+ ' Course'
    },
    confirmModal: function(){
      this.$inertia.replace(route('course_users_delete', this.modal.course_id))
      .then(() => this.modal.showModal = false)
    },
    isMobile: function() {
    	var check = false;
      (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
      return check;
    }
  },
}
</script>
