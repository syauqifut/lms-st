<template>
  <div>


              <div class="w-full lg:w-1/1 xl:w-1/1 px-2">
                <div class=" bg-white rounded-lg mb-6 xl:mb-0">
                  <div class="flex-auto p-4">
                    <div class="flex flex-wrap">
                      
                      <!-- title -->
                      <div class="relative w-full pr-4 py-1 max-w-full flex-grow flex-1">
                        <h5 class="text-gray-800 text-l">
                            <a :href="route('course-modules.get_by_course', courseModules[0].course_id)" class="hover:underline">
                               <i class="fa fa-backward"></i> Go to the Meeting List
                            </a>
                        </h5>     
                      </div>

                      <!-- desc 
                      <p class="text-sm text-gray-600 mt-2 mb-3">
                        This course raises some fundamental eLearning questions. What do you need to succeed as an online instructor? What strategies do eLearning gurus practice? By the end of this course, you'll have all the answers.
                      </p>
                      -->
                      <!-- Button Menu: Start -->
                        
                        
                      <!-- Button Menu: End -->

                      <!-- Section: Start -->
                      <div class="w-full mt-6 bg-gray-100 p-2 pb-6" >
                        <div class="flex justify-between bg-gray-300 rounded-lg mb-4 hover:underline">
                          <a :href="route('detail.course.modules', courseModules[0].id)"                           
                          class=" font-semibold text-lg px-4 py-3">
                             {{courseModules[0].coursename}}-{{courseModules[0].subjectname}}({{courseModules[0].subjectcode}})-{{ courseModules[0].title }}
                          </a>
                          
                         <div v-if="role.is_teacher"  class="text-white p-3 text-center inline-flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-600">
                            <a :href="route('course-modules.edit', courseModules[0].id)">
                                <i class="fa fa-edit"></i>
                            </a>
                              </div>
                        </div>

                         <!-- Date & Time: Start -->
                        <div class="flex flex-wrap pb-4">
                          <div class="w-full lg:w-6/12 xl:w-6/12">
                              <div class="relative flex flex-col min-w-0 break-words mb-6 xl:mb-0">
                                <div class="flex flex-wrap">
                                  <div class="relative w-auto pr-4 flex-initial py-1">
                                    <div class="text-white text-center inline-flex items-center justify-center w-12 h-12 rounded-lg bg-orange-500">
                                      <i class="fa fa-calendar"></i>
                                    </div>
                                  </div>
                                  <div class="relative w-full py-1 pr-4 max-w-full flex-grow flex-1">
                                  
                                    <h5 class="text-gray-800 font-bold text-lg ">
                                      {{ courseModules[0].date_indonesia }} 
                                    </h5>
                                    <h5 class="text-gray-800 mt-3">
                                      {{ courseModules[0].schedule_start_at_indonesia }} - {{ courseModules[0].schedule_end_at_indonesia }}
                                    </h5>
                                  </div>
                                </div>
                              </div>
                          </div>


                          <div class="w-full lg:w-6/12 xl:w-6/12 content-right">
                              <div class="flex flex-wrap">
                                <div class="pr-2">
                                  <div class="flex-wrap bg-blue-600 hover:bg-blue-800 rounded mb-3 xl:mb-0">
                                    <div class="py-3 px-4">
                                          <a :href="route('discussions.index', courseModules[0].id)" class="py-1 text-sm text-white">
                                            <i class="fa fa-comments"></i> Discussion
                                          </a>
                                    </div>
                                  </div>
                                </div>
                                <div class="pr-2">
                                  <div class="flex-wrap bg-green-600 hover:bg-green-800 rounded mb-3 xl:mb-0">
                                    <div class="py-3 px-4">
                                          <a :href="route('presences-show', courseModules[0].id)" class="py-1 text-sm text-white">
                                            <i class="fa fa-gift"></i> Attendance
                                          </a>
                                    </div>
                                  </div>
                                </div>
                                <div class="pr-2">
                                  <div class="flex-wrap bg-purple-600 hover:bg-purple-800 rounded mb-3 xl:mb-0">
                                    <div class="py-3 px-4">
                                          <a :href="route('tasks.index', courseModules[0].id)" class="py-1 text-sm text-white">
                                            <i class="fa fa-question"></i> Task
                                          </a>
                                    </div>
                                  </div>
                                </div>

                              </div>
                          </div>
                        </div>
                        <!-- Date & Time: End -->
                        
                        <hr class="border border-dashed border-gray-300 mb-6 mx-2">
                        
                        <div class="flex flex-wrap">  
                        <div class="pr-2">
                          <div v-if="role.is_teacher" class="flex-wrap bg-green-600 hover:bg-green-800 rounded mb-3 xl:mb-0">
                            <div class="py-3 px-4">
                                  <a  :href="route('create.course.unit', courseModules[0].id)" class="py-1 text-sm text-white">
                                    <i class="fa fa-plus"></i> Create Materi
                                  </a>
                            </div>
                          </div>
                        </div>
                        </div>


                        <!-- Content: Start -->
                        <div class="flex justify-between items-center mt-5" v-for="unit in courseModules[0].unit" :key="unit.id">
                          <div class="text-gray-800 text-center inline-flex items-center ">
                            
                            <i v-if="unit.type_course_units === 1" class="fa fa-newspaper-o mr-4"></i>
                            <i v-else-if="unit.type_course_units === 2" class="fa fa-file mr-4 "></i>
                            <i v-else-if="unit.type_course_units === 3" class="fa fa-link mr-4"></i>
                            <i v-else-if="unit.type_course_units === 4" class="fa fa-video-camera mr-4"></i>
                            <i v-else-if="unit.type_course_units === 5" class="fa fa-youtube mr-4"></i>

                            <a :href="route('course-unit.see', unit.id)" class="text-base font-semibold hover:underline text-gray-800 -ml-2">
                            {{unit.name}}
                          </a>
                          </div>

                            <inertia-link v-if="role.is_teacher" class="fa fa-edit" :href="route('course-units.edit', unit.id)"></inertia-link>
                          
                        </div>

                        <!-- Content: End -->
                      </div>
                      <!-- Section: End -->

                     
                    </div>
                  </div>
                </div>
              </div>




















    <!--
    <div class="flex justify-between items-center mt-20">

      <h1 class="mb-8 font-bold text-xl">
        <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('courses.index')">Kembali ke Daftar Pelajaran</inertia-link>
        <span class="text-indigo-400 font-medium"> / </span>Daftar Materi pada Mata Pelajaran {{dataCourse[0].title}}
        <br>
        <br>
        <span class=" font-medium">
          Mata Pelajaran : {{dataCourse[0].subject.name}}
        </span>
        <br>
        <span class=" font-medium">
          Guru : {{dataCourse[0].teacher.fullname}}
        </span>
        <br>
        <span class=" font-medium">
          Keterangan : {{dataCourse[0].group.classes}}, {{dataCourse[0].group.huruf}}, {{dataCourse[0].group.kel_kelas}}, {{dataCourse[0].category.title}}
        </span>
      </h1>


      <div v-if="role.is_teacher" class="flex">
        <inertia-link class="btn-indigo" :href="route('create.course.modules', dataCourse[0].id)">
          <span>Buat</span>
          <span class="hidden md:inline"> Materi</span>
        </inertia-link>
        <inertia-link class="btn-indigo ml-3" :href="route('reviews.approve', dataCourse[0].id)">
          <span>Approve</span>
          <span class="hidden md:inline"> Reviews</span>
        </inertia-link>
      </div>
      <div v-else>
        <inertia-link class="btn-indigo" :href="route('reviews.create', dataCourse[0].id)">
          <span>Reviews</span>
          <span class="hidden md:inline"> Mata Pelajaran</span>
        </inertia-link>
      </div>
      
    </div>




    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>

    </div>-->


    <!--
           <div v-for="modules in courseModules[0].data" :key="modules.id" class=" w-full mb-3">

             <div class="max-w-full px-4 my-4 py-6 bg-white rounded-lg shadow-md">
                <div class="flex justify-between items-center">
                  <span class="font-light text-gray-800 ">
                     <span class=""><i class="fa fa-calendar "></i> {{ modules.date_indonesia }} / {{ modules.schedule_start_at_indonesia }} - {{ modules.schedule_end_at_indonesia }} </span>

                    <span v-if="modules.actual_start_at_indonesia" class="font-light  text-green-600  mt-2  border-t border-gray-400 lg:border-gray-400">
                       <br>
                       <br>
                      <i class="fa fa-check"></i> {{ modules.actual_start_at_indonesia }} - {{ modules.actual_end_at_indonesia }}
                    </span>
                  </span>


                  <inertia-link class="px-2 py-1 mb-1 bg-gray-600 text-gray-100 font-light rounded hover:bg-gray-500" :href="route('discussions.index', modules.id)">
                      Diskusi
                  </inertia-link>

                  <inertia-link class="px-2 py-1 mb-1 bg-green-600 text-gray-100 font-light rounded hover:bg-gray-500" :href="route('presences-show', modules.id)">
                      <i class="fa fa-gifts"></i> Presensi
                  </inertia-link>
                  <inertia-link class="px-2 py-1 mb-1 bg-blue-600 text-gray-100 font-light rounded hover:bg-gray-500" :href="route('tasks.index', modules.id)">
                      Task
                  </inertia-link>

                  <inertia-link v-if="$page.auth.user.usertype_id  == 1 || $page.auth.user.usertype_id  == 3" class="px-2 py-1 mb-1 bg-yellow-600 text-white font-light rounded hover:bg-yellow-500"  :href="route('course-modules.edit', modules.id)">
                    <span>Ubah</span>
                    <span class="hidden md:inline">Materi</span>
                  </inertia-link>

                </div>


                <div class="mt-2">
                    <a class="text-2xl text-gray-700 font-bold hover:text-gray-800" href="#">
                      {{modules.title}}
                    </a>

                    <div class="mt-10 mb-3">
                      <inertia-link  v-if="$page.auth.user.usertype_id  == 1 || $page.auth.user.usertype_id  == 3"  class="px-2 py-1 mb-1 bg-green-600 text-white font-light rounded hover:bg-green-500" :href="route('create.course.unit', modules.id)">
                        <i class="fa fa-plus"></i>
                        <span>Buat</span>
                        <span class="hidden md:inline">Isi Materi</span>
                      </inertia-link>
                    </div>

                      <div v-for="unit in modules.unit" :key="unit.id" class="flex justify-between items-center mt-5 border-b border-gray-400 lg:border-gray-400">

                        <inertia-link :href="route('course-unit.see', unit.id)">

                          <p  :class="['my-2 font-light', (unit.date_complete ? 'text-green-600 ' : 'text-gray-600 ')]">
                          {{unit.order_course_units}}.
                            <i v-if="unit.type_course_units === 1" class="fa fa-newspaper-o "></i>
                            <i v-else-if="unit.type_course_units === 2" class="fa fa-file"></i>
                            <i v-else-if="unit.type_course_units === 3" class="fa fa-link"></i>
                            <i v-else-if="unit.type_course_units === 4" class="fa fa-video-camera"></i>
                            {{unit.name}}
                          </p>

                          <p  v-if="unit.date_complete" class="text-sm mb-2 font-light">
                            Selesai pada : {{unit.date_complete_indonesia}}
                          </p>
                        </inertia-link>

                      <inertia-link class="fa fa-edit"  v-if="$page.auth.user.usertype_id  == 1 || $page.auth.user.usertype_id  == 3"  :href="route('course-units.edit', unit.id)"></inertia-link>

                    </div>
                </div>
            </div>
          </div>


          <pagination v-if="courseModules[0].data.length != 0" :links="courseModules[0].links" />

           <div v-if="courseModules[0].data.length === 0" class="max-w-full px-4 my-4 py-6 bg-white rounded-lg shadow-md">
              <div class="flex justify-between items-center">
                Belum ada Materi
              </div>
            </div>
            <tr
              v-for="modules in courseModules[0].data"
              :key="modules.id"
              class="hover:bg-gray-100 focus-within:bg-gray-100"
            >-->
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
import draggable from 'vuedraggable'

export default {
  metaInfo: { title: 'Learning Module' },
  layout: Layout,
  components: {
    Icon,
    draggable,
  },
  props: {
    courseModules: Array,
    filters: Object,
  },
  data() {
    return {
      role: {
        is_murid: this.$page.auth.user.usertype_id == 2,
        is_teacher: this.$page.auth.user.usertype_id == 3 || this.$page.auth.user.usertype_id == 1,
      },
    }
  },

  methods: {
      checkMove: function(evt){
          console.log(evt);
      }
  },
}
</script>
