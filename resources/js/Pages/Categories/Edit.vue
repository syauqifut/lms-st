<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('categories.index')"
      >Category</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.title }}
    </h1>
    <trashed-message
      v-if="!category.is_active"
      class="mb-6"
      @restore="restore"
    >Category was deleted.</trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.title"
            :errors="$page.errors.title"
            class="pr-6 pb-8 w-full lg"
            label="Title Category"
          />

          <select-input
            v-model="form.parent_category_id"
            :errors="$page.errors.parent_category_id"
            class="pr-6 pb-8 w-full lg"
            label="Category Parent"
          > 
           <option value=""></option> 
            <option v-for="data in categories" :key="data.id" :value="data.id">{{ data.title }}</option>
          </select-input>

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
            v-if="category.is_active"
            class="text-red-600 hover:underline"
            tabindex="-1"
            type="button"
            @click="destroy"
          >Delete Category</button>
          <loading-button
            :loading="sending"
            class="btn-indigo ml-auto"
            type="submit"
          >Update Category</loading-button>
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
    category: Object,
    categories: Array,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        title: this.category.title,
        is_active: this.category.is_active,
        parent_category_id: this.category.parent_category_id,
      },
    };
  },
  methods: {
    submit() {
      this.sending = true;
      this.$inertia
        .put(this.route("categories.update", this.category.id), this.form)
        .then(() => (this.sending = false));
    },
    destroy() {
      if (confirm("Are you sure you want to delete this category?")) {
        this.$inertia.delete(
          this.route("categories.destroy", this.category.id)
        );
      }
    },
    restore() {
      if (confirm("Are you sure you want to restore this module?")) {
        this.$inertia.put(this.route("categories.restore", this.category.id));
      }
    },
  },
};
</script>
