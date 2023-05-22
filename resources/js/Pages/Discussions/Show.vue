<template>
  <div>
    <!-- <h1 class="mb-8 font-bold text-3xl">Diskusi / {{ discussion.title }}</h1> -->
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('discussions.index', discussion.course_module_id)">Discussion</inertia-link> / {{ discussion.title }}
    </h1>

    <div v-if="userLogin.usertype_id != 2" class="mb-10">
      <inertia-link class="btn-indigo" :href="route('discussions.change_status', discussion.id)">
        <span>{{ discussion.is_active ? 'Close' : 'Open' }}</span>
        <span class="hidden md:inline">Discussion</span>
      </inertia-link>
    </div>
    <div class="max-w rounded mb-5">
      <div class="mb-5">
        <div v-if="discussion.is_active" class="flex items-center bg-blue-500 text-white text-lg font-bold px-4 py-3" role="alert">
          <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" /></svg>
          <p>Active Discussion</p>
        </div>
        <div v-else class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <!-- <strong class="font-bold">Holy smokes!</strong> -->
          <span class="block sm:inline">Discussion has Closed</span>
        </div>
      </div>
      <div class=" bg-teal-200 shadow-lg rounded-lg ">
        <div class="flex items-start px-4 py-6">

          <img v-if="discussion.photo" class="w-12 h-12 rounded-full object-cover mr-4 shadow" :src="discussion.photo" alt="avatar">
          <img v-else class="w-12 h-12 rounded-full object-cover mr-4 shadow" src="/images/default_photo.png" alt="avatar">
          <div class="">
            <div class="flex items-center">
              <h2 class="text-lg font-semibold text-gray-900 mr-10">{{ discussion.user.fullname }} </h2>
              <!-- <small class="text-sm text-gray-700">22h ago</small> -->
              {{ formatDate(discussion.created_at) }}
            </div>
            <!-- <p class="text-gray-700">Joined 12 SEP 2012. </p> -->
            <p class="mt-3 text-gray-700 text-sm">
              {{ discussion.discuss }}
            </p>
            <div class="my-4 flex items-center">
              <!-- {{ discussion.file_attachment ? discussion.file_attachment : 'Tidak ada file' }} -->
              <a v-if="discussion.file_attachment" :href="route('discussion.download_file', discussion.id)" class="text-blue-500 text-lg hover:underline" target="_blank">{{ discussion.file_attachment }}</a>
            </div>
            <button
              v-if="userLogin.usertype_id == 3 || userLogin.usertype_id == 1"
              class="text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-xs px-4 py-2 rounded-full outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease"
              @click="destroy(discussion.id)"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <div
        v-for="comment in discussionComments.data"
        :key="comment.id"
        class=" bg-white shadow-lg rounded-lg "
      >
        <!--horizantil margin is just for display-->
        <div class="flex items-start px-4 py-6">
          <!-- <img class="w-12 h-12 rounded-full object-cover mr-4 shadow" src="https://images.unsplash.com/photo-1542156822-6924d1a71ace?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60" alt="avatar"> -->
          <img v-if="comment.photo" class="w-12 h-12 rounded-full object-cover mr-4 shadow" :src="comment.photo" alt="avatar">
          <img v-else class="w-12 h-12 rounded-full object-cover mr-4 shadow" src="/images/default_photo.png" alt="avatar">
          <div class="">
            <div class="flex items-center">
              <h2 class="text-lg font-semibold text-gray-900 mr-10">{{ comment.user.fullname }} </h2>
              <!-- <small class="text-sm text-gray-700">22h ago</small> -->
              {{ formatDate(comment.created_at) }}
            </div>
            <!-- <p class="text-gray-700">Joined 12 SEP 2012. </p> -->
            <p class="mt-3 text-gray-700 text-sm">
              {{ comment.discuss }}
            </p>
            <div class="my-4 flex items-center">
              <!-- {{ comment.file_attachment ? comment.file_attachment : '' }} -->
              <a v-if="comment.file_attachment" :href="route('discussion.download_file', comment.id)" class="text-blue-500 text-lg hover:underline" target="_blank">{{ comment.file_attachment }}</a>
            </div>
            <button
              v-if="userLogin.usertype_id == 3 || userLogin.usertype_id == 1"
              class="text-red-500 bg-transparent border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-xs px-4 py-2 rounded-full outline-none focus:outline-none mr-1 mb-1" type="button" style="transition: all .15s ease"
              @click="destroy(comment.id)"
            >
              Delete
            </button>
          </div>
        </div>

        <hr>
      </div>
    </div>
    <pagination :links="discussionComments.links" />

    <div class="max-w rounded mb-5 mt-10">
      <div v-if="discussion.is_active" class="bg-white rounded shadow overflow-hidden max-w">
        <form class="w-full max-w bg-white rounded-lg px-4 pt-2" @submit.prevent="submit">
          <div class="flex flex-wrap -mx-3 mb-6">
            <!-- <h2 class="px-4 pt-3 pb-2 text-gray-800 text-lg">Beri komentar</h2> -->
            <div class="w-full md:w-full px-3 mb-2 mt-2">
              <textarea
                v-model="form.discuss"
                :errors="$page.errors.discuss"
                class="bg-gray-100 rounded border border-gray-400 leading-normal resize-none w-full h-20 py-2 px-3 font-medium placeholder-gray-700 focus:outline-none focus:bg-white" name="body" placeholder="Type a comment here" required
              />
            </div>
            <div class="w-full md:w-full flex items-start md:w-full px-3">
              <div class="flex items-start w-1/2 text-gray-700 px-2 mr-auto">
                <!-- <svg fill="none" class="w-5 h-5 text-gray-600 mr-1" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs md:text-sm pt-px">Some HTML is okay.</p> -->
                <file-input v-model="form.file_attachment" :errors="$page.errors.file_attachment" class="pr-6 pb-8 w-full lg" type="file" />
              </div>
              <div class="-mr-1">
                <input type="submit" class="bg-white text-gray-700 font-medium py-1 px-4 border border-gray-400 rounded-lg tracking-wide mr-1 hover:bg-gray-100" value="Post Comments">
              </div>
            </div>
          </div>
        </form>
      </div>

      <div v-else class="bg-white rounded shadow overflow-hidden max-w">
        <div class="bg-blue-100 border-t border-b border-blue-500 text-blue-700 px-4 py-3" role="alert">
          <h3 class="font-bold">This discussion has been closed</h3>
          <p class="text-sm">You cannot comment if the discussion has been closed. But you can still see pre-existing comments</p>
        </div>
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

import moment from 'moment'

export default {
  metaInfo: { title: 'Discussion Details' },
  layout: Layout,
  components: {
    LoadingButton,
    Icon,
    Pagination,
    SearchFilter,
    FileInput,
    TextareaInput,
  },
  props: {
    userLogin: Object,
    discussion: Object,
    discussionComments: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        discuss: null,
        file_attachment: null,
        parent_discuss_id: this.discussion.id,
        course_module_id: this.discussion.course_module_id,
      },
    }
  },
  methods: {
    formatDate(date) {
      return moment(date).format('DD-MM-YYYY HH:mm:ss')
    },
    destroy(id) {
      if (confirm('Are you sure you want to delete ?' + id)) {
        this.$inertia.delete(
          this.route('discussions.destroy', id)
        )
      }
    },
    submit() {
      this.sending = true

      var data = new FormData()
      data.append('discuss', this.form.discuss || '')
      data.append('file_attachment', this.form.file_attachment || '')
      data.append('course_module_id', this.form.course_module_id || '')
      data.append('parent_discuss_id', this.form.parent_discuss_id || '')

      this.$inertia.post(this.route('discussion_comments.store'), data).then(() => {
        this.sending = false
        this.form.discuss = null
        this.form.file_attachment = null
      })
    },
  },
}
</script>
