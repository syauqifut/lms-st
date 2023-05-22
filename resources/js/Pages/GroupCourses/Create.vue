<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('group_courses')"
      >Course</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Buat Course
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
         
         
          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Pilih Course</h1>
            <v-select label="title" :reduce="courses => courses.id" v-model="form.course_id" :options="courses"></v-select>
          </div>

          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Pilih Grup</h1>
            <v-select label="classes" :reduce="groups => groups.id" v-model="form.group_id" :options="groups"></v-select>
          </div>

         
         
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            v-on:click="addAnother"
            class="btn-bland mr-2 cursor-pointer"
          >Buat Grup {{menuName}} dan Tambah Baru</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Buat {{menuName}}</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from "@/Shared/Layout";
import LoadingButton from "@/Shared/LoadingButton";
import SelectInput from "@/Shared/SelectInput";
import TextInput from "@/Shared/TextInput";
import TextareaInput from "@/Shared/TextareaInput";

export default {
  metaInfo: { title: "Buat Group Course" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  props: {
    courses: Array,
    groups: Array,
    menuName: String,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        course_id: null,
        group_id: null,
        addAgain: false,
      },
    };
  },
  methods: {
    addAnother() {
      this.form.addAgain = true;
      const btn = this.$refs.btnsubmit.$el;
      btn.click();
    },
    submit() {
      this.sending = true;
      this.$inertia
        .post(this.route("group_courses.store"), this.form)
        .then(() => {
          
          this.sending = false;
         // this.form.course_id = null;
          //this.form.group_id = null;
          this.form.addAgain = false;

        });
    },
  },
};
</script>
