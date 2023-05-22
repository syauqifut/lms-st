<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('course-modules.get_by_course', course.id)">{{course.title}}</inertia-link> / 
      Reviews
    </h1>
    <div class="flex flex-wrap">
      <div class="w-full">
        <div class="bg-white rounded shadow overflow-hidden lg:w-1/2 ">
          <form @submit.prevent="submit">
            <input type="hidden" v-model="form.addAgain" />
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap text-3xl flex justify-center">
              <vue-stars name="demo" :max="5" v-model="form.star" :readonly="false" />
            </div>
            <textarea-input
                    v-model="form.review"
                    :errors="$page.errors.description"
                    class="pr-6 pb-8 w-full lg ml-3"
                    label="Description"
                  />
            <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
              <loading-button
                ref="btnsubmit"
                :loading="sending"
                class="btn-indigo"
                type="submit"
              >Submit</loading-button>
            </div>
          </form>
        </div>
      </div>
      
      <div class="overflow-hidden mt-6 lg:w-1/2 w-full p-4">
      <h1 class="mb-8 font-bold text-2xl">
        All Reviews on this Class
      </h1>
        <div
          v-for="review in reviews"
          :key="review.id"
          class=" bg-white shadow-lg rounded-lg my-3"
        >
          <div class="flex items-start px-4 py-6">
            <img class="w-12 h-12 rounded-full object-cover mr-4 shadow" src="https://images.unsplash.com/photo-1542156822-6924d1a71ace?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60" alt="avatar">
            <div class=" w-full">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mr-10">{{ review.student.fullname }} - {{review.course.title}}</h2>
                {{ formatDate(review.created_at) }}
              </div>
              <p class="text-gray-700 text-lg flex justify-between">
                <vue-stars :max="5" :name="'all'+review.id" :value="parseInt(review.star)" :readonly="true" />
              </p>
              <p class="my-3 text-gray-700 text-sm mb-3">
                {{ review.review }}
              </p>
            </div>
          </div>

        </div>
      </div>
      <div class="overflow-hidden mt-6 lg:w-1/2 w-full  p-4">
      <h1 class="mb-8 font-bold text-2xl">
        My Reviews
      </h1>
        <div
          v-for="review in myreviews"
          :key="review.id"
          class=" bg-white shadow-lg rounded-lg my-3"
        >
          <div class="flex items-start px-4 py-6">
            <img class="w-12 h-12 rounded-full object-cover mr-4 shadow" src="https://images.unsplash.com/photo-1542156822-6924d1a71ace?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60" alt="avatar">
            <div class=" w-full">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mr-10">{{ review.student.fullname }} - {{review.course.title}}</h2>
                {{ formatDate(review.created_at) }}
              </div>
              <p class="text-gray-700 text-lg flex justify-between">
                <vue-stars :max="5" :name="'my'+review.id" :value="parseInt(review.star)" :readonly="true" />
                <span>{{status[review.is_active]}}</span>
              </p>
              <p class="my-3 text-gray-700 text-sm mb-3">
                {{ review.review }}
              </p>
              <div v-if="review.is_active == 0">
                <button class="text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-xs px-4 py-2 rounded-full outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease"
                  @click="destroy(review.id)" > Delete</button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    
  </div>
</template>
 
<script>
import Layout from "@/Shared/Layout";
import LoadingButton from "@/Shared/LoadingButton";
import SelectInput from "@/Shared/SelectInput";
import TextInput from "@/Shared/TextInput";
import TextareaInput from "@/Shared/TextareaInput";
import moment from 'moment'
import { VueStars } from "vue-stars"

export default {
  metaInfo: { title: "Reviews" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    VueStars
  },
  props: {
    course: Object,
    reviews:Array,
    myreviews:Array,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        star:0,
        review:null,
        course_id: this.course.id,
      },
      status:{
        '-2' : "Dihapus",
        '-1' : "Ditolak",
        '0' : "Proses Approval",
        '1' : "Diterima",
      },
    };
  },
  methods: {
    formatDate(date) {
      return moment(date).subtract({ hours: 7 }).format('DD-MM-YYYY HH:mm:ss')
    },
    submit() {
      this.sending = true;
      this.$inertia.post(this.route("reviews.store"), this.form).then(() => {
        this.sending = false;
      });
    },
    destroy(id) {
      if (confirm('Are you sure you want to delete the review ?' + id)) {
        this.$inertia.delete(
          this.route('reviews.destroy', id)
        )
      }
    },
  },
};
</script>
