<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link v-if="$page.auth.user.usertype_id == 1 || $page.auth.user.usertype_id == 4" class="text-indigo-400 hover:text-indigo-600" :href="route('courses.index')">Learning</inertia-link>
      <inertia-link v-else class="text-indigo-400 hover:text-indigo-600" :href="route('courses.index')">Learning</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ course.title}}
      <span class="text-xl">{{' - ' + course.join_code}}</span>
    </h1>


<!-- component -->

    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10 " x-data="{welcomeMessageShow:true}" x-show="welcomeMessageShow" x-transition:enter="transition-all ease duration-500 transform" x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition-all ease duration-500 transform" x-transition:leave-end="opacity-0 scale-90">
           <div class="w-full flex flex-wrap">
              <div class="w-full sm:w-1/2 md:w-2/4 px-3 text-left">
                  <div class="p-5 xl:px-8 md:py-5">
                      <h3 class="text-2xl mb-3">{{course.title}}</h3>
                      <h5 class="text-xl mb-3">{{course.group.classes}} - {{course.group.huruf}}</h5>
                      <h5 class="text-xl mb-3">{{course.group.kel_kelas}} / {{course.group.year}}</h5>
                      <h5 class="text-xl mb-3">{{course_teacher.fullname}}</h5>
                  </div>
              </div>

              <div  class="w-full sm:w-1/2 md:w-2/4 px-3 text-left">
                <div v-if="$page.auth.user.usertype_id == 1  || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4">
                <form @submit.prevent="submit" ref="form">

                    <div class="pr-6 pb-8 w-full lg:w-1/1">
                      <h1 class="mb-1">Choose User</h1>
                      <v-select label="fullname" :reduce="user_for_select_option => user_for_select_option.id" v-model="form.user_id" :options="user_for_select_option"></v-select>
                    </div>

                   <div class="px-5  bg-gray-100  flex justify-start items-center">
                    <loading-button ref="sbbtn" :loading="sending" class="btn-indigo" type="submit">Add User</loading-button>
                  </div>
                </form>
                </div>
                <div v-else class="flex justify-end">
                  <!--<span class="bg-transparent cursor-pointer hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded"
                      v-on:click="toggleModal()">Leave</span>-->

                </div>
              </div>

            </div>

    </div>
    

    <div class="w-full mt-6 bg-gray-100 p-2 pb-6" >
      <div class="flex justify-between bg-gray-300 rounded-lg mb-4 hover:underline">
        <a href="#"                           
        class=" font-semibold text-lg px-4 py-3">
          List Teacher
        </a>
      </div>

      <div class="flex justify-between items-center mt-5" v-for="user_in_group in gurus" :key="user_in_group.id">
          <div class="text-gray-800 text-center inline-flex items-center ">
            <i class="fa fa-user mr-4"></i>
            <a href="#" class="text-base font-semibold hover:underline text-gray-800 -ml-2">
              {{user_in_group.fullname}} 
            </a>
          </div>

          <div v-if="($page.auth.user.usertype_id == 1  || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4) && user_in_group.is_active == 1" @click="destroy(user_in_group.user_id,user_in_group.fullname)" class="cursor-pointer">           
            <span class="inline-block bg-green-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2">
               <i class="fa fa-check "></i> Active
            </span>
          </div>  
          <div v-else>

             <div v-if="($page.auth.user.usertype_id == 1  || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4) && user_in_group.is_active != 1"  class="cursor-pointer"  @click="activated(user_in_group.user_id,user_in_group.fullname)">
           
              <span class="inline-block bg-red-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2">
                <i class="fa fa-remove "></i> Not Active
              </span>
            </div> 
          </div>         
      </div>
      
      <div v-if="gurus.length === 0">
          <div class="bg-white shadow-lg rounded-lg hover:bg-gray-100 w-full px-4 py-4 mb-2">
          <div class="flex items-center justify-between">
              <h2 class="text-lg  text-gray-700 ">No Teacher Data.</h2>
          </div>
        </div>
      </div>
    </div>





    <div class="w-full mt-6 bg-gray-100 p-2 pb-6" >
      <div class="flex justify-between bg-gray-300 rounded-lg mb-4 hover:underline">
        <a href="#"                           
        class=" font-semibold text-lg px-4 py-3">
          List Students
        </a>
      </div>

      <div class="flex justify-between items-center mt-5" v-for="user_in_group in users_in_group" :key="user_in_group.id">
          <div class="text-gray-800 text-center inline-flex items-center ">
            <i class="fa fa-user mr-4"></i>
            <a href="#" class="text-base font-semibold hover:underline text-gray-800 -ml-2">
              {{user_in_group.fullname}} 
            </a>
          </div>


          <div v-if="($page.auth.user.usertype_id == 1  || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4) && user_in_group.is_active == 1" @click="destroy(user_in_group.user_id,user_in_group.fullname)" class="cursor-pointer">           
            <span class="inline-block bg-green-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2">
               <i class="fa fa-check "></i> Active
            </span>
          </div>  
          <div v-else>

             <div v-if="($page.auth.user.usertype_id == 1  || $page.auth.user.usertype_id == 3 || $page.auth.user.usertype_id == 4) && user_in_group.is_active != 1"  class="cursor-pointer"  @click="activated(user_in_group.user_id,user_in_group.fullname)">
           
              <span class="inline-block bg-red-200 rounded-full px-3 py-2 text-sm font-normal text-gray-700 mr-2">
                <i class="fa fa-remove "></i> Not Active
              </span>
            </div> 
          </div>                
      </div>
      <div v-if="users_in_group.length === 0">
          <div class="bg-white shadow-lg rounded-lg hover:bg-gray-100 w-full px-4 py-4 mb-2">
          <div class="flex items-center justify-between">
              <h2 class="text-lg  text-gray-700 ">No Student Data.</h2>
          </div>
        </div>
      </div>


    </div>

    <modal-confirmation :showModal="modal.showModal" :titleModal="modal.titleModal" :descModal="modal.descModal" v-on:toogle="toggleModal" v-on:confirm="confirmModal"> </modal-confirmation>

  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import ModalConfirmation from '@/Shared/ModalConfirmation'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    ModalConfirmation,
  },

  props: {
    group: Object,
    course: Object,
    users_in_group: Array,
    gurus: Array,
    user_for_select_option: Array,
    teacher: Object,
    course_teacher: Object,
  },
  remember: 'form',
  data() {
    return {
      status:{
        '-2' : 'Keluar',
        '-1' : 'Ditolak',
        '0' : 'Pending',
        '1' : 'Aktif',
      },
      modal: {
        showModal: false,
        descModal: 'Are you sure you want to leave this Learning?',
        titleModal: "Leave Learning",
      },
      sending: false,
      forGroup: {
        name: this.group.classes,
        classes: this.group.classes,
        mainteacher: this.group.mainteacher,
        academicterms: this.group.academicterms,
        year: this.group.year,
      },
      form: {
        user_id : null,
        group_id: this.group.id,
        course_id: this.course.id,
      },
    }
  },
  methods: {
    submit() {

      this.sending = true
      var data = new FormData()

      data.append('user_id', this.form.user_id || '')
      data.append('course_id', this.form.course_id || '')

      this.$inertia.post(this.route('course_users_store'), data)
        .then(() =>
        {
          this.sending = false
          this.form.user_id = null
        })

    },
    destroy(user_id, fullname) {
      if (confirm('are you sure to delete Data '+fullname+'?')) {
        this.$inertia.replace(this.route('course_users_delete', [this.course.id, user_id]))
      }
    },
    activated(user_id, fullname) {
      if (confirm('Are you sure to Activate '+fullname+'?')) {
        this.$inertia.replace(this.route('course_users_activated', [this.course.id, user_id]))
      }
    },
    toggleModal: function(){
      this.modal.showModal = !this.modal.showModal;
    },
    confirmModal: function(){
      this.$inertia.replace(route('course_users_delete', this.course.id))
      .then(() => this.modal.showModal = false)
    }
  },
}
</script>
