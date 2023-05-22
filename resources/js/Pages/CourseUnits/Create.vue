<!--

1 =     text
2 =     file ([pdf,image])
3 =     web_url
4 =     vidcon_link

!-->

<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('detail.course.modules',dataCourseModules.id)" 
      >
        {{dataCourseModules.title}}
      </inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Materi
    </h1>
    <div class="flex-1 lg:mt-0 mt-3">
        <div class="bg-white mx-auto p-6">
          <form>
            <form @submit.prevent="submit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <select-input
                v-model="form.create_unit"
                :errors="$page.errors.create_unit"
                class="pr-6 pb-8 w-full lg"
                label="Build Type Materi"
              >
                <option value="new" selected>Create New Materi</option>
                <option value="old">Choose From Old Materi</option>
              </select-input>
              <div v-if="form.create_unit == 'old'" class="pr-6 pb-8 w-full lg:w-1/1">
                <h1 class="mb-1">Materi</h1>
                <v-select v-model="form.course_unit_id" :errors="$page.errors.create_unit" label="name" :reduce="courseUnits => courseUnits.id" :options="courseUnits" />
              </div>
                <text-input
                  v-model="form.name"
                  :errors="$page.errors.name"
                  class="pr-6 pb-8 w-full lg"
                  label="Name Materi"
                />
                <div v-if="form.create_unit == 'new'" class="pr-6 pb-8 w-full lg">
                  <label for="select-input-15" class="form-label">Materi Type :</label>
                  <select name="LeaveType"
                    @change="onChange($event)"

                    class="form-select mb-5"
                    v-model="form.type_course_units">
                      <!--<option value="1">Text</option>-->
                      <option value="2">File pdf, xls, doc, ppt </option>
                      <option value="3">Link Web Url</option>
                      <option value="4">Link Video Conference</option>
                      <option value="5">Video Youtube</option>
                      <option value="6">Image (png,jpg,gif)</option>
                    </select>
                </div>

                <textarea-input
                  v-model="form.text"
                  :errors="$page.errors.text"
                  v-if="show_text"
                  class="pr-6 pb-8 w-full lg"
                  label="Materi form Text"

                />
                <text-input
                  v-model="form.web_url"
                  :errors="$page.errors.web_url"
                  v-if="show_web_url"
                  class="pr-6 pb-8 w-full lg"
                  label="URL Web"
                />
                <file-input
                  v-model="form.file_name"
                  :errors="$page.errors.file_name"
                  v-if="show_file_name"
                  class="pr-6 pb-8 w-full lg"
                  type="file"
                  label="pdf, xls, doc, ppt"
                />
                <file-input
                  v-model="form.file_name_img"
                  :errors="$page.errors.file_name_img"
                  v-if="show_file_name_img"
                  class="pr-6 pb-8 w-full lg"
                  type="file"
                  label="Image (png,jpg,gif)"
                />
                <text-input
                  v-model="form.vidcon_link"
                  :errors="$page.errors.vidcon_link"
                  v-if="show_vidcon_link"
                  class="pr-6 pb-8 w-full lg"
                  label="Link Video Conference"
                />
                <text-input
                  v-model="form.youtube"
                  :errors="$page.errors.youtube"
                  v-if="show_youtube"
                  class="pr-6 pb-8 w-full lg"
                  label="Url Video Youtube"
                />
                <text-input
                  v-model="form.order_course_units"
                  :errors="$page.errors.order_course_units"
                  class="pr-6 pb-8 lg"
                  label="Order Materi"
                />
                <select-input
                  v-model="form.unit_is_active"
                  :errors="$page.errors.unit_is_active"
                  class="pr-6 pb-8 w-full lg"
                  label="Status"
                >
                  <option value="0">Not Active</option>
                  <option value="1" selected>Active</option>
                </select-input>
                <loading-button
                  :loading="sending"
                  class="btn-indigo ml-auto"
                  type="submit"
                >
                  Save Materi
                </loading-button>
              </div>
            </form>
          </form>
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
import TextareaInput from "@/Shared/TextareaInput";
import Icon from '@/Shared/Icon'

import moment from 'moment'

export default {
  metaInfo: { title: 'Create Learning Module' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
    TextareaInput,
  },
  remember: 'form',
  props: {
    courseUnits: Array,
    dataCourseModules: Object ,
    orderNext : Number ,
  },
  data() {

    return {

      sending: false,
      show_file_name   : false,
      show_file_name_img   : false,
      show_web_url     : false,
      show_text        : false,
      show_vidcon_link : false,
      show_youtube : false,

      form: {
        create_unit: 'new',
        unit_is_active      : 1,
        order_course_units   : this.orderNext,
        link_to_unit_id   : null,
        file_name         : null,
        file_name_img     : null,
        text              : null,
        name              : null,
        type_course_units : null,
        course_module_id  : this.dataCourseModules.id,

      },
    }
  },
  methods: {
/**

1 =     text
2 =     document
3 =     web_url
4 =     vidcon_link
5 =     image

*/


    onChange(event) {
      console.log("Show "+event.target.value+ " fields")

      if (event.target.value == '1'){
        this.show_text          = true;
        this.show_file_name     = false;
        this.show_file_name_img = false;
        this.show_web_url       = false;
        this.show_vidcon_link   = false;
        this.show_vidcon_link   = false;
        this.show_youtube       = false;
      }

      else if (event.target.value == '2'){
        this.show_file_name     = true;
        this.show_file_name_img = false;
        this.show_text          = false;
        this.show_vidcon_link   = false;
        this.show_web_url       = false;
        this.show_youtube       = false;
      }

      else if (event.target.value == '3'){
        this.show_web_url       = true;
        this.show_file_name_img = false;
        this.show_vidcon_link   = false;
        this.show_file_name     = false;
        this.show_text          = false;
        this.show_youtube       = false;
      }

      else if (event.target.value == '4'){
        this.show_vidcon_link   = true;
        this.show_file_name_img = false;
        this.show_web_url       = false;
        this.show_text          = false;
        this.show_file_name     = false;
        this.show_youtube       = false;
      }
      else if (event.target.value == '5'){
        this.show_youtube       = true;
        this.show_file_name_img = false;
        this.show_web_url       = false;
        this.show_text          = false;
        this.show_file_name     = false;
        this.show_vidcon_link   = false;
      }
      else if (event.target.value == '6'){
        this.show_file_name_img = true;
        this.show_youtube       = false;
        this.show_web_url       = false;
        this.show_text          = false;
        this.show_file_name     = false;
        this.show_vidcon_link   = false;
      }
      else{
        this.show_vidcon_link   = false;
        this.show_web_url       = false;
        this.show_text          = false;
        this.show_file_name     = false;
        this.show_file_name_img = false;
        this.show_youtube       = false;
      }
    },
    submit() {
      this.sending = true

      var dataUnit = new FormData()
      dataUnit.append('web_url', this.form.web_url || '')
      dataUnit.append('file_name', this.form.file_name || '')
      dataUnit.append('file_name_img', this.form.file_name_img || '')
      dataUnit.append('name', this.form.name || '')
      dataUnit.append('vidcon_link', this.form.vidcon_link || '')
      dataUnit.append('youtube', this.form.youtube || '')
      dataUnit.append('text', this.form.text || '')
      dataUnit.append('link_to_unit_id', this.form.link_to_unit_id || '')
      dataUnit.append('unit_is_active', this.form.unit_is_active || '')
      dataUnit.append('course_module_id', this.dataCourseModules.id || '')
      dataUnit.append('type_course_units', this.form.type_course_units || '')
      dataUnit.append('order_course_units', this.form.order_course_units || '')
      dataUnit.append('create_unit', this.form.create_unit || '')
      dataUnit.append('course_unit_id', this.form.course_unit_id || '')

      this.$inertia
        .post(this.route('course-units.store'), dataUnit)
        .then(() => {
          this.form.web_url           = null,
          this.form.file_name         = null,
          this.form.file_name_img     = null,
          this.form.vidcon_link       = null,
          this.form.youtube       = null,
          this.form.link_to_unit_id   = null,
          this.form.type_course_units = null,
          this.form.course_module_id  = null,
          this.form.name              = null,
          this.form.text              = null,
          this.form.order_course_units              = null,
          this.sending                = false
        })
    },
  },
}
</script>
