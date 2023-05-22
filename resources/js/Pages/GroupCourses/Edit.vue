<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('group_courses')"
      >{{menuName}}</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{menuName}}
    </h1>
    <trashed-message
      v-if="group_courses.deleted_at"
      class="mb-6"
      @restore="restore"
    >{{menuName}} ini telah dihapus.</trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
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
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button
            v-if="!group_courses.deleted_at"
            class="text-red-600 hover:underline"
            tabindex="-1"
            type="button"
            @click="destroy"
          >Hapus {{menuName}}</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Perbarui {{menuName}}</loading-button>
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
import TrashedMessage from "@/Shared/TrashedMessage";

export default {
  metaInfo() {
    return {
      title: `${this.form.name}`,
    };
  },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    TrashedMessage,
  },
  props: {
    group_courses: Object,
    courses: Array,
    groups: Array,
    menuName: String,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        course_id: this.group_courses.course_id,
        group_id: this.group_courses.group_id,
      },
    };
  },
  methods: {
    submit() {
      this.sending = true;
      this.$inertia
        .put(
          this.route("group_courses.update", this.group_courses.id),
          this.form
        )
        .then(() => (this.sending = false));
    },
    destroy() {
      if (confirm("Apakah kamu yakin ingin menghapus modul ini?")) {
        this.$inertia.delete(
          this.route("group_courses.destroy", this.group_courses.id)
        );
      }
    },
    restore() {
      if (confirm("Apakah kamu yakin ingin me-restore modul ini?")) {
        this.$inertia.put(
          this.route("group_courses.restore", this.group_courses.id)
        );
      }
    },
  },
};
</script>
