<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('subject_modules')"
      >Modules</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message
      v-if="subjectModule.deleted_at"
      class="mb-6"
      @restore="restore"
    >Modul ini telah dihapus.</trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
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
            <option value="0">Tidak Aktif</option>
            <option value="1">Aktif</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button
            v-if="!subjectModule.deleted_at"
            class="text-red-600 hover:underline"
            tabindex="-1"
            type="button"
            @click="destroy"
          >Hapus Subject</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Perbarui Modul</loading-button>
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
    subjectModule: Object,
    subjects: Array,
    groups: Array,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        name: this.subjectModule.name,
        description: this.subjectModule.description,
        is_active: this.subjectModule.is_active,
        subject_id: this.subjectModule.subject_id,
        group_id: this.subjectModule.group_id,
      },
    };
  },
  methods: {
    submit() {
      this.sending = true;
      this.$inertia
        .put(
          this.route("subject_modules.update", this.subjectModule.id),
          this.form
        )
        .then(() => (this.sending = false));
    },
    destroy() {
      if (confirm("Apakah kamu yakin ingin menghapus modul ini?")) {
        this.$inertia.delete(
          this.route("subject_modules.destroy", this.subjectModule.id)
        );
      }
    },
    restore() {
      if (confirm("Apakah kamu yakin ingin me-restore modul ini?")) {
        this.$inertia.put(
          this.route("subject_modules.restore", this.subjectModule.id)
        );
      }
    },
  },
};
</script>
