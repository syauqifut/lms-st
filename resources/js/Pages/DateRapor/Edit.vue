<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('daterapors.index')"
      >Rapor Date</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ groups.classes }}
    </h1>
    <trashed-message
      v-if="!daterapor.is_active"
      class="mb-6"
      @restore="restore"
    >Rapor Date was deleted.</trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model= groups 
            class="pr-6 pb-8 w-full lg"
            label="Group"
            disabled="validated == 1"
          />

          <text-input
            v-model="form.date_rapor"
            type="date"
            :errors="$page.errors.date_rapor"
            class="pr-6 pb-8 w-full lg"
            label="Tanggal Rapor"
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
            v-if="daterapor.is_active"
            class="text-red-600 hover:underline"
            tabindex="-1"
            type="button"
            @click="destroy"
          >Delete Rapor Date</button>
          <loading-button
            :loading="sending"
            class="btn-indigo ml-auto"
            type="submit"
          >Update Rapor Date</loading-button>
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
      title: `${this.form.group_id}`,
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
    daterapor: Object,
    daterapors: Array,
    groups: Object,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        group_id: this.daterapor.group_id,
        date_rapor: this.daterapor.date_rapor,
        is_active: this.daterapor.is_active,
      },
    };
  },
  methods: {
    submit() {
      this.sending = true;
      this.$inertia
        .put(this.route("daterapors.update", this.daterapor.id), this.form)
        .then(() => (this.sending = false));
    },
    destroy() {
      if (confirm("Are you sure you want to delete this Report Date?")) {
        this.$inertia.delete(
          this.route("daterapors.destroy", this.daterapor.id)
        );
      }
    },
    restore() {
      if (confirm("Are you sure you want to restore this Report Date?")) {
        this.$inertia.put(this.route("daterapors.restore", this.daterapor.id));
      }
    },
  },
};
</script>
