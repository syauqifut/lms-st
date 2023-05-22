<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tasks.get_student_upload', task.task_id)">Task</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Preview
    </h1>
    <div class="flex-1 lg:mt-0 mt-3">
      <!-- <div class="flex justify-between bg-gray-200 mb-5">
        <div  v-if="prevUnit > 0">
          <inertia-link  :href="route('course-unit.see',prevUnit)" class="text-gray-700 text-center bg-gray-400 px-4 py-2 m-2">
            <i class="fa fa-arrow-left" v-if="!isMobile()"></i> Materi Sebelumnya
          </inertia-link>
        </div>
        <div v-else ></div> -->


        <!-- <div v-if="nextUnit > 0">
          <inertia-link :href="route('course-unit.see',nextUnit)" class="text-gray-700 text-center bg-gray-400 px-4 py-2 m-2">
            Materi Selanjutnya <i class="fa fa-arrow-right" v-if="!isMobile()"></i>
          </inertia-link>
        </div>
        <div v-else ></div>
      </div> -->

      <div class="bg-white mx-auto w-full p-6">

        <div class="min-h-screen">
          
          <div class="min-h-screen">
            <div v-if="showPdf != ''" class="flex justify-center p-6 text-center min-h-screen">
              <iframe :src="showPdf" width="100%" frameborder="0"></iframe>
            </div>
            <div v-else-if="showGoogle != ''" class="flex justify-center p-6 text-center min-h-screen">
              <iframe :src="showGoogle" width="100%" frameborder="0"></iframe>
            </div>
            <div v-else class="flex justify-center p-6 text-center min-h-screen">
              <iframe width="100%" v-bind:src="'/files/student_tasks/' + task.file"></iframe>
            </div>
          </div>
          <!-- <div  class="min-h-screen" v-else>
            <div v-if="showGoogle != ''" class="flex justify-center p-6 text-center min-h-screen">
              <iframe :src="showGoogle" width="100%" frameborder="0"></iframe>
            </div>
          </div> -->

          <!--<div class="mt-10">
            <center>
              <a target="_blank" class="btn-indigo" :href="route('download_task_file', taskFile.id)">Download Task</a> 
            </center>
          </div>-->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import FileInput from '@/Shared/FileInput'
import Icon from '@/Shared/Icon'

import PdfViewer from '@/Shared/PdfViewer'

import moment from 'moment'

export default {
  metaInfo: { title: 'Preview Task' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
    PdfViewer,
  },
  remember: 'form',
  props: {
    task          : Object,
    file          : Object,
    showGoogle    : String,
    showPdf       : String,
  },
  data() {

    return {
    }
  },
  methods: {
      oke(){
        console.log(("asd"));
      },
      isMobile: function() {
    	var check = false;
      (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
      return check;
    }
  },
}
</script>
