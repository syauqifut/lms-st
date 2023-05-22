<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('detail.course.modules',dataCourseModules.id)"
      >
        {{ dataCourseModules.title }}
      </inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Detail Materi
    </h1>
    <div class="flex-1 lg:mt-0 mt-3">
      <div class="flex justify-between bg-gray-200 mb-5">
        <div  v-if="prevUnit > 0">
          <inertia-link  :href="route('course-unit.see',prevUnit)" class="text-gray-700 text-center bg-gray-400 px-4 py-2 m-2">
            <i class="fa fa-arrow-left" v-if="!isMobile()"></i> Materi Previous
          </inertia-link>
        </div>
        <div v-else ></div>


        <div v-if="nextUnit > 0">
          <inertia-link :href="route('course-unit.see',nextUnit)" class="text-gray-700 text-center bg-gray-400 px-4 py-2 m-2">
            Materi Next <i class="fa fa-arrow-right" v-if="!isMobile()"></i>
          </inertia-link>
        </div>
        <div v-else ></div>
      </div>

      <div class="bg-white mx-auto w-full p-6">
        <div v-if="this.cekPrevUnitNotComplete > 0" class="bg-white w-full mx-auto p-6 text-center">
          <p mb-10>
            The materi cannot be displayed if you are not done with the materi
            <b>
              <a class="underline" :href="route('course-unit.see',cekPrevUnitNotComplete)">
                {{ this.namePrevUnitNotComplete }}
              </a>
            </b>
          </p>
        </div>

        <div v-else class="min-h-screen">
          <div v-if="dataCourseUnit.type_course_units == '1'" class="bg-white w-full mx-auto p-6 text-center">
            <p mb-10>Please read the following text :</p>
            <br>
            <p>{{ dataCourseUnit.content }}</p>
          </div>
          <div v-else ></div>


          <div v-if="dataCourseUnit.type_course_units == '2'" class="min-h-screen">
            <div v-if="showGoogle != ''" class="flex justify-center p-6 text-center min-h-screen">
              <iframe :src="showGoogle" width="100%" frameborder="0"></iframe>
            </div>
            <div v-else class="flex justify-center p-6 text-center min-h-screen">
              <!-- <iframe width="100%" v-bind:src="'/files/course_units/' + dataCourseUnit.content"></iframe> -->
              <!-- <iframe src="http://docs.google.com/gview?url=http://infolab.stanford.edu/pub/papers/google.pdf&embedded=true" width="100%" frameborder="0"></iframe> -->
              <iframe :src="showPdf" width="100%" frameborder="0"></iframe>
            </div>
          </div>
          <div v-else ></div>

          <div v-if="dataCourseUnit.type_course_units == '3'" class="flex justify-center bg-white w-full mx-auto p-6 text-center min-h-screen">
            <iframe v-if="dataCourseUnit.type_course_units  == '3'" width="100%" class="object-center sm:object-top md:object-right lg:object-bottom xl:object-left mb-4" :src="dataCourseUnit.content" allowfullscreen></iframe>
          </div>
          <div v-if="dataCourseUnit.type_course_units == '3'" class="bg-white w-full mx-auto p-6 text-center">
            <p mb-10>If it can't be displayed, please open the Web Url Link :</p>
            <br>
            <a class="underline" :href="dataCourseUnit.content" target="_BLANK">
              <p>{{ dataCourseUnit.content }}</p>
            </a>
          </div>

          <div v-if="dataCourseUnit.type_course_units == '4'" class="bg-white w-full mx-auto p-6 text-center">
            <p mb-10>Please open the Video Conference Link :</p>
            <br>
            <a class="underline" :href="dataCourseUnit.content" target="_BLANK">
              <p>{{ dataCourseUnit.content }}</p>
            </a>
          </div>
          <div v-else ></div>


          <iframe v-if="dataCourseUnit.type_course_units  == '5'" width="100%" class="flex justify-center p-6 text-center min-h-screen object-center sm:object-top md:object-right lg:object-bottom xl:object-left" :src="embedYoutube" allowfullscreen></iframe>

          <div v-if="dataCourseUnit.type_course_units == '6'" class="min-h-screen">
            <div class="flex justify-center p-6 text-center min-h-screen">
              <iframe width="100%" v-bind:src="'/files/course_units/' + dataCourseUnit.content"></iframe>
            </div>
          </div>
          <div v-else ></div>

          <hr>
          <div v-if="$page.auth.user.usertype_id == '2'">
            <div v-if="dataCourseUnitComplete">
              <div v-if="dataCourseUnitComplete.date_complete_indonesia">
                <center>
                  <p class="mt-2 mb-3 text-color-green ">It's been resolved on {{ dataCourseUnitComplete.date_complete_indonesia }}</p>
                  <form class="mt-10">
                    <center>
                      <inertia-link class="btn-indigo" :href="route('course-unit.cancel_complete',dataCourseUnit.id)">Cancel Done</inertia-link>
                    </center>
                  </form>
                </center>
              </div>

              <div v-else>
                <form class="mt-10">
                  <center>
                    <p class="text-xs mb-3 text-color-green ">If you have completed the materi, please click Finish</p>
                    <br>
                    <inertia-link class="btn-indigo" :href="route('course-unit.complete',dataCourseUnit.id)">Finish</inertia-link>
                  </center>
                </form>
              </div>
            </div>
            <div v-else>
              <form class="mt-10">
                <center>
                  <p class="text-xs mb-3 text-color-green ">If you have completed the materi, please click Finish</p>
                  <br>
                  <inertia-link class="btn-indigo" :href="route('course-unit.complete',dataCourseUnit.id)">Finish</inertia-link>
                </center>
              </form>
            </div>
          </div>

          <div v-else class="mt-10">
            <center>
              <inertia-link class="btn-indigo" :href="route('course-units.edit', dataCourseUnit.id )+'?from=see '">Edit Materi</inertia-link>
            </center>
          </div>
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
  metaInfo: { title: 'See Learning Unit' },
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
    dataCourseModules       : Object ,
    showGoogle              : String ,
    showPdf                 : String ,
    dataCourseUnit          : Object ,
    dataCourseUnitComplete  : Object ,
    typeUnit                : String ,
    namePrevUnitNotComplete   : String ,
    embedYoutube   : String ,
    prevUnit                  : Number ,
    nextUnit                  : Number ,
    cekPrevUnitNotComplete    : Number ,
  },
  data() {

    return {
      sending: false,
      show_file_name   : this.dataCourseUnit.file_name,
      show_file_name_img   : this.dataCourseUnit.file_name_img,
      show_web_url     : this.dataCourseUnit.web_url,
      show_vidcon_link : this.dataCourseUnit.vidcon_link,
      show_youtube: this.dataCourseUnit.youtube,
      form: {
        unit_is_active  : 1,
        link_to_unit_id : null,
        file_name_img   : this.dataCourseUnit.file_name_img,
        web_url         : this.dataCourseUnit.web_url,
        vidcon_link     : this.dataCourseUnit.vidcon_link,
        youtube         : this.dataCourseUnit.youtube,
        unit_type       : this.typeUnit,
        course_module_id: this.dataCourseModules.id,

      },
       pdf:{
        url: "/files/course_unit/"+this.dataCourseUnit.file_name,
        title: "Bodea Brochure.pdf",
      }
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
