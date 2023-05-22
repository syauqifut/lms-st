<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('detail.course.modules', courseModule.id)">{{ courseModule.title }}</inertia-link>                                                                              
      <span class="text-indigo-400 font-medium">/</span> Discussion  
    </h1>

    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('discussions.create', module.id)">
        <span>Create</span>
        <span class="hidden md:inline">Discussion</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Title</th>
          <th class="px-6 pt-6 pb-4">Message</th>
          <th class="px-6 pt-6 pb-4">File Attachment</th>
          <th class="px-6 pt-6 pb-4">Created By</th>
          <th class="px-6 pt-6 pb-4">Status</th>
        </tr>
        <tr
          v-for="discussion in discussions.data"
          :key="discussion.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('discussions.show', [module.id, discussion.id])"
            >
              {{ discussion.title }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('discussions.show', [module.id, discussion.id])"
            >
              {{ discussion.discuss }}
            </inertia-link>
          </td>
          <td class="border-t">
            <div v-if="discussion.file_attachment">
              <button class="rounded bg-blue-500 text-white font-semibold shadow-lg px-2 py-2 hover:bg-blue-400" @click="openModal(discussion)">
                View File
              </button>
            </div>
            <div v-else>
              'No files'
            </div>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('discussions.show', [module.id, discussion.id])"
            >
              {{ discussion.user.fullname }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('discussions.show', [module.id, discussion.id])"
            >
              {{ discussion.is_active ? 'ACTIVE' : 'ALREADY CLOSED' }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link
              class="px-4 flex items-center"
              :href="route('discussions.show', [module.id, discussion.id])"
              tabindex="-1"
            >
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="discussions.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No Discussion</td>
        </tr>
      </table>
    </div>
    <pagination :links="discussions.links" />

    <div v-if="toggleModal" class="fixed overflow-x-hidden overflow-y-auto inset-0 flex justify-center items-center z-50">
      <div class="relative mx-auto w-4/5 max-w-full">
        <div class="bg-white px-5 py-5 w-full rounded shadow-2xl flex flex-col">
          <div class="text-2xl font-bold">File : {{ discussionModal.file_attachment }}</div>
          <hr class="my-5">

          <div v-if="extension == 'No preview available'" class="min-h-full">
            <div class="flex justify-center p-6 text-center min-h-full">
              <span>{{ extension }}</span>
            </div>
          </div>
          <div v-else>
            <div class="flex justify-center p-6 text-center min-h-full">
              <iframe class="" width="100%" height="500px" v-bind:src="viewEmbed"></iframe>
            </div>
          </div>
          <hr class="my-5">
          <div class="flex flex-row justify-center">
            <a :href="route('discussion.download_file', discussionModal.id)" class="rounded bg-blue-500 text-white px-5 py-2 mr-5" target="_blank">Download File</a>
            <button class="rounded bg-green-500 text-white px-5 py-2" @click="toggleModal = false">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="toggleModal" class="absolute inset-0 z-40 opacity-25 bg-black"></div>
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
  metaInfo: { title: 'Discussion' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    courseModule: Object,
    discussions: Object,
    filters: Object,
  },
  data() {
    return {
      toggleModal: false,
      extension: null,
      viewEmbed: null,
      discussionModal: null,
      module: this.courseModule,
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
            'discussions.index',
            Object.keys(query).length ? query : { remember: 'forget' }
          )
        )
      }, 150),
      deep: true,
    },
  },
  methods: {
    openModal: function(discussion) {
      this.extension = discussion.cekExt
      this.viewEmbed = discussion.viewEmbed
      this.discussionModal = discussion
      this.toggleModal = !this.toggleModal

    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
