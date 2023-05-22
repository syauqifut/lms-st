<template>
  <div>
   <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10">
      <h1 class="mb-8 font-bold text-3xl">Reports by Student</h1>

      <!-- <div class="w-full flex flex-wrap">
        <div class="w-full sm:w-1/2 md:w-2/4 px-3 text-left">
          <div class="p-5 xl:px-8 md:py-5">
            <h3 class="text-2xl mb-3">{{info[0]['nama']}}</h3>
            <h6 class="text-xl mb-3">NIM : {{info[0]['nim']}}</h6>
            <h6 class="text-xl mb-3">Kelas/Walikelas : {{info[0]['kelas']}}/{{info[0]['walikelas']}}</h6>
          </div>
        </div>
      </div> -->
      <div class="mb-6 flex items-center">
        <div class="w-1/4 h-12 py-2 px-5">
          <h1 class="mb-1">Group :</h1>
          <v-select v-model="form.kelas" label="classes" :reduce="kelas => kelas.id" :options="kelas" />
        </div>
        <div class="w-1/4 h-12 py-6">
          <button
            class="btn-indigo hover:underline"
            tabindex="-1"
            type="button"
            @click="cari"
          >
            SEARCH 
          </button>
        </div>
      </div>
    </div>

    <div v-if="group">
      <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10">
        <h1 class="mb-8 font-bold text-3xl">Reports by Student Data</h1>
        <div class="mb-6 flex justify-between items-center" v-if="report.length !== 0">
          <a class="btn-indigo" :href="route('report-siswa-pdf', [info[0]['nama'], info[0]['kelas'], infokelas])">
            <span>Download Report</span>
          </a>
        </div>
        <div class="bg-white rounded shadow overflow-x-auto">
          <table class="w-full whitespace-no-wrap">
            <tr class="text-left font-bold">
              <th class="px-6 pt-6 pb-4">Subjects</th>
              <th class="px-6 pt-6 pb-4">Teacher</th>
              <th class="px-6 pt-6 pb-4">Tugas</th>
              <th class="px-6 pt-6 pb-4">UTS</th>
              <th class="px-6 pt-6 pb-4">UAS</th>
              <th class="px-6 pt-6 pb-4">Perform</th>
              <th class="px-6 pt-6 pb-4">Sakit</th>
              <th class="px-6 pt-6 pb-4">Izin</th>
              <th class="px-6 pt-6 pb-4">Alpha</th>
              <th class="px-6 pt-6 pb-4">Score</th>
              <th class="px-6 pt-6 pb-4">Alphabet</th>
            </tr> 
            <tr
              v-for="report in report"
              :key="report.id"
              class="hover:bg-gray-100 focus-within:bg-gray-100"
            >  
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.subject }} 
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.gurupengajar }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.tugas }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.uts }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.uas }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.perform }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.sakit }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.izin }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.alpha }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.nilai }}
              </td>
              <td class="border-t px-6 py-4 items-center focus:text-indigo-500">
                  {{ report.huruf }}
              </td>

            </tr>
            <tr v-if="report.length === 0">
              <td class="border-t px-6 py-4" colspan="11">No report data.</td>
            </tr> 
          </table>  
        </div>
      </div>
    </div> 
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'

export default {
  metaInfo: { title: 'Report by Student' },
  layout: Layout,
  components: {

  },
  props: {
    kelas: Array,
    report: Array,
    info: Object,
    murid: Object,
    group: Object,
    infokelas: Object
  },
  data() {
    return {
      sending: false,
      form: {
        // matkul: this.info[0]['subject'],
        // kls: this.info[0]['kelas'],
      },
    }
  },

  methods: {
    cari() {
      let kelas = this.form.kelas == null ? '' : this.form.kelas
      this.$inertia.replace(this.route('report.siswa',
        {
          kelas: kelas,
        }
      )
      ).then(() => {
        // this.form.child_id = this.anak.id
      })
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
