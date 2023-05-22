<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('schedules.index')">Jadwal</inertia-link> / 
      {{date}}
    </h1>
    <div>
      <select-input v-model="form.filter" :errors="$page.errors.filter" class="pr-6 pb-8 w-full lg:w-1/2" label="Filter">
        <option :value="null"></option>
        <option value="c">Course</option>
        <option value="j">Jadwal</option>
      </select-input>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <div
        v-for="schedule in schedules"
        :key="schedule.id"
        class=" bg-white shadow-lg rounded-lg "
      >
        <!--horizantil margin is just for display-->
        <inertia-link :href="schedule.route">
        <div class="flex items-start px-4 py-6">
          <div class="w-1/4">
            {{schedule.date}}
          </div>
          <div class="w-3/4">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold text-gray-900 mr-10">{{ schedule.title }}</h2>
            </div>
          </div>
        </div>
        </inertia-link>

        <hr>
      </div>
    </div>

  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import mapValues from 'lodash/mapValues'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'

export default {
  metaInfo: { title: 'Detail Jadwal' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    Icon,
    SearchFilter,
  },
  props: {
    schedules: Array,
    date: String,
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
        filter:null,
      },
    }
  },
  
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('menus'))
      }, 150),
      deep: true,
    },
  },
  methods: {
    destroy(id) {
      if (confirm('Apakah kamu yakin ingin menolak reviews ?' + id)) {
        this.$inertia.replace(
          this.route('reviews.decline', id)
        )
      }
    },
  },
}
</script>
