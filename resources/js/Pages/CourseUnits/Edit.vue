<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('course-modules.get_by_course',dataCourseModules.course_id)"
      >
        {{dataCourseModules.title}}
      </inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Update Materi
    </h1>
    <div class="flex-1 lg:mt-0 mt-3">
        <div class="bg-white mx-auto p-6">
          <form>
            <form @submit.prevent="submit">
              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <text-input
                  v-model="form.name"
                  :errors="$page.errors.name"
                  class="pr-6 pb-8 w-full lg"
                  label="Name Materi"
                />
                <div class="pr-6 pb-8 w-full lg">
                  <label for="select-input-15" class="form-label">Materi Type :</label> 
                  <select name="LeaveType" 
                    @change="onChange($event)" 
                    
                    class="form-select mb-5" 
                    v-model="form.type_course_units">
                      <!--<option value="1">Text</option>-->
                      <option value="2">File pdf, xls, doc, ppt</option>
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
                  label="Materi from Text"
                  
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
                
                <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Materi</button>
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
  metaInfo: { title: 'Edit Materi' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    FileInput,
  },
  remember: 'form',
  props: {
    dataCourseModules : Object ,
    dataCourseUnit    : Object ,
    typeUnit          : String ,
    fromLink          : String ,
    show_file_name    : Boolean ,
    show_file_name_img: Boolean ,
    show_web_url      : Boolean ,      
    show_text         : Boolean ,
    show_vidcon_link  : Boolean ,
    show_youtube  : Boolean ,
  },
  data() {

    return {
      sending: false,      
      form: {        

        unit_is_active    : this.dataCourseUnit.is_active,
        link_to_unit_id   : null,
        name              : this.dataCourseUnit.name,
        order_course_units              : this.dataCourseUnit.order_course_units,

        file_name         : null,
        file_name_img     : null,
        vidcon_link       : this.dataCourseUnit.content,
        text              : this.dataCourseUnit.content,
        web_url           : this.dataCourseUnit.content,
        youtube           : this.dataCourseUnit.content,

        type_course_units : this.dataCourseUnit.type_course_units,
        course_module_id  : this.dataCourseModules.id,

      },
    }
  },
  methods: {
    
    onChange(event) {
      console.log(event.target.value);

      if (event.target.value == '1'){
        this.show_text        = true;

        this.show_file_name   = false;
        this.show_file_name_img   = false;
        this.show_web_url     = false;
        this.show_vidcon_link = false;
        this.show_youtube = false;

        if(this.dataCourseUnit.type_course_units == '1'){
          this.form.vidcon_link     = null;
          this.form.file_name       = null;
          this.form.file_name_img   = null;
          this.form.web_url         = null;
          this.form.youtube         = null;

           this.form.text           = this.dataCourseUnit.content;
        }       
        
      }
      
      if (event.target.value == '2'){
        this.show_file_name     = true;
        this.show_file_name_img   = false;
        this.show_text          = false;
        this.show_vidcon_link   = false;
        this.show_web_url       = false;
        this.show_youtube       = false;

        this.form.text          = null;
        this.form.vidcon_link   = null;
        this.form.web_url       = null;
        this.form.file_name_img   = null;
        this.form.youtube       = null;
      }

      if (event.target.value == '3'){
        this.show_web_url     = true;
        this.show_file_name_img   = false;
        this.show_vidcon_link   = false;
        this.show_file_name     = false;
        this.show_text          = false;
        this.show_youtube       = false;

        if(this.dataCourseUnit.type_course_units == '3'){
          this.form.vidcon_link   = null;
          this.form.file_name     = null;
          this.form.text          = null;
          this.form.youtube       = null;
          this.form.file_name_img   = null;
           this.form.web_url          = this.dataCourseUnit.content;
        }
        
      }
      
      if (event.target.value == '4'){
        this.show_vidcon_link   = true;
        this.show_file_name_img   = false;
        this.show_web_url       = false;
        this.show_text          = false;
        this.show_file_name     = false;
        this.show_youtube       = false;

        if(this.dataCourseUnit.type_course_units == '4'){
          this.form.web_url       = null;
          this.form.text          = null;
          this.form.file_name     = null;
          this.form.youtube       = null;
          this.form.file_name_img   = null;
          this.form.vidcon_link          = this.dataCourseUnit.content;
        }
       
      }

      if (event.target.value == '5'){
        this.show_file_name_img   = false;
        this.show_youtube   = true;
        this.show_web_url       = false;
        this.show_text          = false;
        this.show_file_name     = false;
        this.show_vidcon_link       = false;

        if(this.dataCourseUnit.type_course_units == '5'){
          this.form.web_url       = null;
          this.form.text          = null;
          this.form.file_name     = null;
          this.form.vidcon_link   = null;
          this.form.file_name_img   = null;
           this.form.youtube      = this.dataCourseUnit.content;
        }
       
      }

      if (event.target.value == '6'){
        this.show_file_name     = false;
        this.show_file_name_img   = true;
        this.show_text          = false;
        this.show_vidcon_link   = false;
        this.show_web_url       = false;
        this.show_youtube       = false;

        this.form.text          = null;
        this.form.vidcon_link   = null;
        this.form.web_url       = null;
        this.form.file_name   = null;
        this.form.youtube       = null;
      }
    },
    submit() {
      this.sending = true

      var dataUnit = new FormData()
      dataUnit.append('name', this.form.name || '')
      
      dataUnit.append('fromLink', this.fromLink )

      dataUnit.append('web_url', this.form.web_url || '')
      dataUnit.append('file_name', this.form.file_name || '')
      dataUnit.append('file_name_img', this.form.file_name_img || '')
      dataUnit.append('vidcon_link', this.form.vidcon_link || '')
      dataUnit.append('text', this.form.text || '')
      dataUnit.append('youtube', this.form.youtube || '')

      dataUnit.append('link_to_unit_id', this.form.link_to_unit_id || '')
      dataUnit.append('unit_is_active', this.form.unit_is_active || '')
      dataUnit.append('course_module_id', this.dataCourseModules.id || '')
      dataUnit.append('type_course_units', this.form.type_course_units || '') 
      dataUnit.append('order_course_units', this.form.order_course_units || '') 

       dataUnit.append('_method', 'put')

      this.$inertia
        //.post(this.route('course-units.update'), dataUnit)
        .post(this.route('course-units.update', this.dataCourseUnit.id), dataUnit)
        //.then(() => {
        .then(() => {
          this.sending                = false          
        })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this data?')) {
        this.$inertia.delete(this.route('course-unit.destroy', this.dataCourseUnit.id))
      }
    },
  },
}
</script>
