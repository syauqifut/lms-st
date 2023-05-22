<template>
  <div v-if="reviews.length  < 1">
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link v-if="role.is_teacher" class="text-indigo-400 hover:text-indigo-600" :href="route('course-modules.get_by_course', courseData.id)">{{courseData.title}} / </inertia-link>  
      Reviews
    </h1>

    <div class="bg-white rounded shadow overflow-x-auto p-4">
        No Reviews Yet
    </div>
  </div>
  <div v-else>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link v-if="role.is_teacher" class="text-indigo-400 hover:text-indigo-600" :href="route('course-modules.get_by_course', reviews[0].course.id)">{{reviews[0].course.title}} /</inertia-link>  
      Reviews
    </h1>

    <div class="bg-white rounded shadow overflow-x-auto">
      <div
        v-for="review in reviews"
        :key="review.id"
        class=" bg-white shadow-lg rounded-lg "
      >
        <!--horizantil margin is just for display-->
        <div class="flex items-start px-4 py-6">
          <img class="w-12 h-12 rounded-full object-cover mr-4 shadow" src="https://images.unsplash.com/photo-1542156822-6924d1a71ace?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60" alt="avatar">
          <div class=" w-full">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold text-gray-900 mr-10">{{ review.student.fullname }} - {{review.course.title}}</h2>
              {{ formatDate(review.created_at) }}
            </div>
            <p class="text-gray-700 text-lg flex justify-between">
              <vue-stars :max="5" :name="''+review.id" :value="parseInt(review.star)" :readonly="true" />
              <span>{{status[review.is_active]}}</span>
            </p>
            <p class="my-3 text-gray-700 text-sm mb-3">
              {{ review.review }}
            </p>
            <div v-if="review.is_active > -2 ">

              <div v-if="role.is_murid">
                <button class="text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-xs px-4 py-2 rounded-full outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease"
                  @click="destroy(review.id)" > Hapus</button>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('reviews.edit', review.id)">Edit</inertia-link> 
              </div>
              <div v-else-if="role.is_teacher && review.is_active ==0">
                <inertia-link class="btn-indigo p-3" :href="route('reviews.approve', review.id)">Approve</inertia-link> 
                <button class="text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-xs px-4 py-2 rounded-full outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease"
                  @click="destroy(review.id)" > Refuse</button>
              </div>
            </div>
          </div>
        </div>

        <hr>
      </div>
    </div>

    

  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'
import FileInput from '@/Shared/FileInput'
import TextareaInput from '@/Shared/TextareaInput'
import { VueStars } from "vue-stars"
import moment from 'moment'

export default {
  metaInfo: { title: 'Reviews' },
  layout: Layout,
  components: {
    LoadingButton,
    Icon,
    Pagination,
    SearchFilter,
    FileInput,
    TextareaInput,
    VueStars
  },
  props: {
    reviews: Array,
    user: Object,
    courseData: Object,
  },
  remember: 'form',
  data() {
    return {
      role: {
        is_murid: this.$page.auth.user.usertype_id == 2,
        is_teacher: this.$page.auth.user.usertype_id == 3 || this.$page.auth.user.usertype_id == 1,
      },
      sending: false,
      status:{
        '-2' : "Dihapus",
        '-1' : "Ditolak",
        '0' : "Proses Approval",
        '1' : "Diterima",
      },
      form: {
        
      },
    }
  },
  methods: {
    formatDate(date) {
      return moment(date).subtract({ hours: 7 }).format('DD-MM-YYYY HH:mm:ss')
    },
    destroy(id) {
      if (confirm('Are you sure you want to refuse the review ?' + id)) {
        this.$inertia.replace(
          this.route('reviews.decline', id)
        )
      }
    },
  },
}
</script>
