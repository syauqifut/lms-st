<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">{{menuName}}</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        
      </search-filter>
      <!--menghapus tombol add tipe user-->
    </div>
    <div v-for="usertype in usertypes.data" :key="usertype.id" >
        <inertia-link  :href="route('usertypes.edit', usertype.id)">
        <div class="bg-white shadow-lg rounded-lg hover:bg-gray-100 w-full px-4 py-4 mb-2">
            <div class="flex items-start justify-between">
                <h2 class="text-lg  text-gray-700 ">{{usertype.id}}. {{usertype.name}}</h2>
                <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400 " />
            </div>  
        </div>
        </inertia-link>        
      </div>

        <div v-if="usertypes.data.length === 0">
           <div class="bg-white shadow-lg rounded-lg hover:bg-gray-100 w-full px-4 py-4 mb-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg  text-gray-700 ">No Data</h2>
            </div>  
          </div>
        </div>

    <pagination :links="usertypes.links" />
    
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
  metaInfo: { title: 'Groups' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    usertypes: Object,
    filters: Object,
    menuName: String,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
      },
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('usertypes', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
