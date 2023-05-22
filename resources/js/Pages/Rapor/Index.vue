<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <span>Generate Report</span>
    </h1>
    <div v-if="authusers==1 || authusers==3|| authusers==4" class="mb-6 flex justify-between items-center">
    <!-- <div  class="mb-6 flex justify-between items-center"> -->
      <a class="btn-indigo" :href="route('rapor.create')">
        <span>Report Table</span>
      </a>
    </div>
   <div v-if="authusers==1 || authusers==3|| authusers==4">
    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10" v-if="isMobile()">
      <h1  class="mb-8 font-bold text-3xl">Report Search</h1>
      <div  class="mb-6 flex items-center">
        <div class="w-full h-12 py-2 px-5" >
          <h1 class="mb-1">Subject :</h1>
          <v-select v-model="form.subject" label="name" :reduce="matkul => matkul.id" :options="matkul" />
        </div>
      </div>
      <div  class="mb-6 flex items-center">
        <div class="w-full h-12 py-2 px-5">
          <h1 class="mb-1">Group :</h1>
          <v-select v-model="form.kelas" label="classes" :reduce="kelas => kelas.id" :options="kelas" />
        </div>
      </div>
      <br>
      <div  class="mb-6 flex justify-between items-center">
        <div class="h-12 py-6 pr-8">
          <button
            class="btn-indigo hover:underline"
            tabindex="-1"
            type="button"
            @click="cari"
          >
            SEARCH 
          </button>
        </div>
        <div v-if="authusers==1 || authusers==3|| authusers==4" class="h-12 py-6">
          <inertia-link
            v-if="form.is_generateall == 1 && form.subject != null && form.kelas != null" 
              class="btn-indigo hover:underline px-4 flex items-center"
              :href="route('rapor.generateall', [form.subject, form.kelas])"
              tabindex="-1"
            >
              Generate All
          </inertia-link>
        </div>
      </div>
    </div>
    
    <!-- <div class="mb-6 flex justify-between items-center" v-if="!isMobile()">
      <div class="w-1/4 h-12 py-2 px-5">
        <h1 class="mb-1">Subject</h1>
        <v-select v-model="form.cari" class="w-full" label="name" :reduce="matkul => matkul.name" :options="matkul"></v-select>
      </div>
      <div class="w-1/4 h-12 py-2 px-5">
        <h1 class="mb-1">Name</h1>
        <v-select v-model="form.search" class="w-full" label="classes" :reduce="kelas => kelas.classes" :options="kelas"></v-select>
      </div>
      <inertia-link class="btn-indigo" @click="cari">
        <span>Cari</span>
      </inertia-link>
      <div class="mb-6 flex justify-between items-center">
      <inertia-link class="btn-indigo" :href="route('rapor.create')">
        <span>Tabel</span>
        <span class="hidden md:inline">Rapor</span>
      </inertia-link>
      </div>
    </div> -->
    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10" v-if="!isMobile()">
      <h1 class="mb-8 font-bold text-3xl">Report Search</h1>
      <div class="mb-6 flex items-center">
        <div class="w-1/4 h-12 py-2 px-5" >
          <h1 class="mb-1">Subject :</h1>
          <v-select v-model="form.subject" label="name" :reduce="matkul => matkul.id" :options="matkul" />
        </div>
        <div class="w-1/4 h-12 py-2 px-5">
          <h1 class="mb-1">Group :</h1>
          <v-select v-model="form.kelas" label="classes" :reduce="kelas => kelas.id" :options="kelas" />
        </div>
        <div class="h-12 py-6 pr-8">
          <button
            class="btn-indigo hover:underline"
            tabindex="-1"
            type="button"
            @click="cari"
          >
            SEARCH 
          </button>
        </div>
        <div v-if="authusers==1 || authusers==3|| authusers==4" class="h-12 py-6">
          <inertia-link
            v-if="form.is_generateall == 1 && form.subject != null && form.kelas != null" 
              class="btn-indigo hover:underline px-4 flex items-center"
              :href="route('rapor.generateall', [form.subject, form.kelas])"
              tabindex="-1"
            >
              Generate All
          </inertia-link>
        </div>
      </div>
    </div>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">NIM</th>
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Group</th>
          <th class="px-6 pt-6 pb-4">Subject</th>
          <th class="px-6 pt-6 pb-4">Teacher</th>
          <th class="px-6 pt-6 pb-4">Status</th>
        </tr>
        <tr
          v-for="rapor in rapor.data"
          :key="rapor.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <!-- <inertia-link
              v-if="rapor.raporid != null"
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.edit', rapor.raporid )"
            >
              {{ rapor.username }} 
            </inertia-link>
            <inertia-link
              v-else
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.editrapor', [rapor.id, rapor.courseid] )"
            >
              {{ rapor.username }} 
            </inertia-link> -->
            {{ rapor.username }} 
          </td>
          <td class="border-t">
            <!-- <inertia-link
              v-if="rapor.raporid != null" 
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.edit', rapor.raporid )"
            >
              {{ rapor.fullname }}
            </inertia-link>
            <inertia-link
              v-else
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.editrapor', [rapor.id, rapor.courseid] )"
            >
              {{ rapor.fullname }} 
            </inertia-link> -->
            {{ rapor.fullname }} 
          </td>
          
          <td class="border-t">
            <!-- <inertia-link
              v-if="rapor.raporid != null"
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.edit', rapor.raporid )"
            >
              {{ rapor.group }}
            </inertia-link>
            <inertia-link
              v-else
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.editrapor', [rapor.id, rapor.courseid] )"
            >
              {{ rapor.group }} 
            </inertia-link> -->
            {{ rapor.group }} 
          </td> 
          <td class="border-t">
            <!-- <inertia-link
              v-if="rapor.raporid != null"
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.edit', rapor.raporid )"
            >
              {{ rapor.subject }}
            </inertia-link>
            <inertia-link
              v-else
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.editrapor', [rapor.id, rapor.courseid] )"
            >
              {{ rapor.subject }} 
            </inertia-link> -->
            {{ rapor.subject }}
          </td>
          <td class="border-t">
            <!-- <inertia-link
              v-if="rapor.raporid != null"
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.edit', rapor.raporid )"
            >
              {{ rapor.teacher }}
            </inertia-link>
            <inertia-link
              v-else
              class="px-6 py-4 flex items-center focus:text-indigo-500"
              :href="route('rapor.editrapor', [rapor.id, rapor.courseid] )"
            >
              {{ rapor.teacher }} 
            </inertia-link> -->
            {{ rapor.teacher }}
          </td>
          <td v-if="authusers==1 || authusers==3|| authusers==4" class="border-t">
            <inertia-link v-if="rapor.raporid != null" class="inline-block bg-red-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" :href="route('rapor.edit', rapor.raporid )" >
              EDIT 
            </inertia-link>
            <inertia-link v-else class="inline-block bg-blue-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" :href="route('rapor.editrapor', [rapor.id, rapor.courseid] )">
              GENERATE 
            </inertia-link>
          </td>
          <td v-else class="border-t">
           
            <inertia-link class="inline-block bg-blue-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 ml-3 cursor-pointer" :href="route('rapor.editrapor', [rapor.id, rapor.courseid] )">
              View 
            </inertia-link>
          </td>
          

          <td class="border-t w-px">
            <inertia-link
              class="px-4 flex items-center"
              :href="route('rapor.editrapor', [rapor.id, rapor.subjectid])"
              tabindex="-1"
            >
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="rapor.data.length === 0">
          <td class="border-t px-6 py-4" colspan="7">No Report Data.</td>
        </tr>
      </table>
      
    </div>
    <pagination :links="rapor.links" />
  </div>
</template>

<script>
import Icon from "@/Shared/Icon";
import Layout from "@/Shared/Layout";
import mapValues from "lodash/mapValues";
import Pagination from "@/Shared/Pagination";
import pickBy from "lodash/pickBy";
import SearchFilter from "@/Shared/SearchFilter";
import throttle from "lodash/throttle";
import TextInput from "@/Shared/TextInput";


export default {
  metaInfo: { title: "Generate Report" },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    // SearchFilter,
    TextInput,
  },
  props: {
    rapor: Object,
    sakit: Number,
    matkul: Array,
    kelas: Array,
    authusers:Object,
    // filters: Object,
  },
  data() {
    return {
      form: {
        // search: this.filters.search,
        // trashed: this.filters.trashed,
        subject: null,
        kelas: null,
        is_generateall: null
      },
    };
  },
  // watch: {
  //   form: {
  //     handler: throttle(function () {
  //       let query = pickBy(this.form);
  //       this.$inertia.replace(
  //         this.route(
  //           "rapor.index",
  //           Object.keys(query).length ? query : { remember: "forget" }
  //         )
  //       );
  //     }, 150),
  //     deep: true,
  //   },
    
  // },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    cari() {
      // this.sending = true
      let subject = this.form.subject == null ? '' : this.form.subject
      let kelas = this.form.kelas == null ? '' : this.form.kelas
      this.form.is_generateall = this.form.is_generateall == null ? 1 : this.form.is_generateall
      this.$inertia.replace(this.route('rapor.index',
        {
          subject: subject,
          kelas: kelas,
        }
      )
      ).then(() => {
        // this.form.child_id = this.anak.id
      })
    },
    isMobile: function() {
    	var check = false;
      (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
      return check;
    }
  },
};

</script>