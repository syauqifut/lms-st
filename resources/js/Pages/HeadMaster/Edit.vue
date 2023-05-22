<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('headmasters.index')"
      >Head Master</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.title }}
    </h1>
    <trashed-message
      v-if="!headmaster.is_active"
      class="mb-6"
      @restore="restore"
    >Head Master was deleted.</trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.name"
            :errors="$page.errors.name"
            class="pr-6 pb-8 w-full lg"
            label="Head Master Name"
          />

          <text-input
            v-model="form.tahun_ajaran"
            :errors="$page.errors.tahun_ajaran"
            class="pr-6 pb-8 w-full lg"
            label="Year"
          />

          <select-input
            v-model="form.is_active"
            :errors="$page.errors.is_active"
            class="pr-6 pb-8 w-full lg"
            label="Status"
          >
            <option value="0">Not Active</option>
            <option value="1">Active</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button
            v-if="headmaster.is_active"
            class="text-red-600 hover:underline"
            tabindex="-1"
            type="button"
            @click="destroy"
          >Delete Head Master</button>
          <loading-button
            :loading="sending"
            class="btn-indigo ml-auto"
            type="submit"
          >Update Head Master</loading-button>
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
      title: `${this.form.title}`,
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
    headmaster: Object,
    headmasters: Array,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        name: this.headmaster.name,
        tahun_ajaran: this.headmaster.tahun_ajaran,
        is_active: this.headmaster.is_active,
      },
    };
  },
  methods: {
    submit() {
      this.sending = true;
      this.$inertia
        .put(this.route("headmasters.update", this.headmaster.id), this.form)
        .then(() => (this.sending = false));
    },
    destroy() {
      if (confirm("Are you sure you want to delete this Head Master?")) {
        this.$inertia.delete(
          this.route("headmasters.destroy", this.headmaster.id)
        );
      }
    },
    restore() {
      if (confirm("Are you sure you want to restore this Head Master?")) {
        this.$inertia.put(this.route("headmasters.restore", this.headmaster.id));
      }
    },
  },
};
</script>
