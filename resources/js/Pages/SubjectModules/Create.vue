<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('subject_modules')"
      >Subject Modules</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Buat Subject Module
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.name"
            :errors="$page.errors.name"
            class="pr-6 pb-8 w-full lg"
            label="Nama modul"
          />
          <textarea-input
            v-model="form.description"
            :errors="$page.errors.description"
            class="pr-6 pb-8 w-full lg"
            label="Deskripsi"
          />

          <select-input
            v-model="form.subject_id"
            :errors="$page.errors.subject_id"
            class="pr-6 pb-8 w-full lg"
            label="Subject"
          >
            <option
              v-for="subject in subjects"
              :key="subject.id"
              :value="subject.id"
            >{{ subject.name }}</option>
          </select-input>

          <select-input
            v-model="form.group_id"
            :errors="$page.errors.group_id"
            class="pr-6 pb-8 w-full lg"
            label="Group"
          >
            <option v-for="group in groups" :key="group.id" :value="group.id">{{ group.classes }}</option>
          </select-input>
          <select-input
            v-model="form.is_active"
            :errors="$page.errors.is_active"
            class="pr-6 pb-8 w-full lg"
            label="Status"
          >
            <!-- <option :value="null" /> -->
            <option value="1" selected>Aktif</option>
            <option value="0">Tidak Aktif</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            v-on:click="addAnother"
            class="btn-bland mr-2 cursor-pointer"
          >Buat Modul dan Tambah Baru</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Buat Modul</loading-button>
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
  metaInfo: { title: "Buat Subjects" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  props: {
    subjects: Array,
    groups: Array,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        name: null,
        description: null,
        subject_id: null,
        group_id: null,
        is_active: 1,
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
        .post(this.route("subject_modules.store"), this.form)
        .then(() => {
          this.sending = false;
          this.form.name = null;
          this.form.description = null;
          this.form.is_active = 1;
          this.form.subject_id = null;
          this.form.group_id = null;
          this.form.addAgain = false;
        });
    },
  },
};
</script>
