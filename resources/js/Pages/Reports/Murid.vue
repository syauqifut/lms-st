<template>
  <div>
    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10" v-if="isMobile()">
      <h1 class="mb-8 font-bold text-3xl">Reports By Student</h1>
      <div class="mb-6 flex items-center" >
        <div class="w-full h-12 py-2 px-5" >
          <h1 class="mb-1">Student :</h1>
          <v-select  v-model="form.murid" label="fullname" :reduce="murid => murid.id" :options="murid" />
        </div>
      </div>
      <div class="mb-6 flex items-center">
        <div class="w-full h-12 py-2 px-5" >
          <h1 class="mb-1">Group :</h1>
          <v-select v-model="form.kelas" label="classes" :reduce="kelas => kelas.id" :options="kelas" />
        </div>
      </div>
      <br>
      <div class="mb-6 flex items-center">
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
    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10" v-if="!isMobile()">
      <h1 class="mb-8 font-bold text-3xl">Report By Student</h1>
      <div class="mb-6 flex items-center">
        <div class="w-1/4 h-12 py-2 px-5">
          <h1 class="mb-1">Student :</h1>
          <v-select  v-model="form.murid" label="fullname" :reduce="murid => murid.id" :options="murid" />
        </div>
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

    <div  v-if="siswa">
      <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10">
        <h1 class="mb-8 font-bold text-3xl">Report by Student Data</h1>
        <h1 class="mb-8 font-bold text-3xl" v-if="info.length !== 0 && infokelas.length !== 0">{{info[0]['nama'] }}/{{ infokelas[0]['classes'] }}</h1>
        
        <div class="mb-6 flex justify-between items-center" v-if="report.length !== 0">
          <a class="btn-indigo" :href="route('report-siswa-pdf-tes', [info[0]['nama'], info[0]['kelas'], kelasreport])">
          <!-- <a class="btn-indigo" :href="route('report-siswa-pdf-tes', [info[0]['nama'], info[0]['kelas'], info[0]['course_id']])"> -->
            <span>Download Report</span>
          </a>
        </div>
        <div class="bg-white rounded shadow overflow-x-auto">
          <table class="w-full whitespace-no-wrap">
            <tr class="text-left font-bold">
              <th class="px-6 pt-6 pb-4">Subject</th>
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
    murid: Array,
    kelas: Array,
    report: Array,
    info: Object,
    infokelas: Object,
    kelasreport: Object,
    siswa: Object,
    group: Object,
  },
  data() {
    return {
      sending: false,
      form: {
        
      },
    }
  },

  methods: {
    cari() {
      // this.sending = true
      let murid = this.form.murid == null ? '' : this.form.murid
      this.$inertia.replace(this.route('report.murid',
        {
          murid: murid,
        }
      )
      ).then(() => {
        // this.form.child_id = this.anak.id
      })
    },
    cari() {
      let murid = this.form.murid == null ? '' : this.form.murid
      let kelas = this.form.kelas == null ? '' : this.form.kelas
      this.$inertia.replace(this.route('report.murid',
        {
          murid: murid,
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
    isMobile: function() {
    	var check = false;
      (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
      return check;
    }
  },
}
</script>
