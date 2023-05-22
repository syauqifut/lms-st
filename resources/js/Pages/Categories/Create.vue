<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('categories.index')"
      >Category</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Category
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.title"
            :errors="$page.errors.title"
            class="pr-6 pb-8 w-full lg"
            label="Title Category"
          />

          <select-input
            v-model="form.parent_category_id"
            :errors="$page.errors.category_parent_id"
            class="pr-6 pb-8 w-full lg"
            label="Category Parent"
          >
          <option value="" selected></option>
            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.title }}</option>
          </select-input>

          <select-input
            v-model="form.is_active"
            :errors="$page.errors.is_active"
            class="pr-6 pb-8 w-full lg"
            label="Status"
          >
            <option value="1" selected>Active</option>
            <option value="0">Not Active</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            v-on:click="addAnother"
            class="btn-bland mr-2 cursor-pointer"
          >Create and Add new Category</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Create Category</loading-button>
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
  metaInfo: { title: "Create Category" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  remember: "form",
  props: {
    categories: Array,
  },
  data() {
    return {
      sending: false,
      form: {
        title: null,
        category_parent_id: null,
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
      this.$inertia.post(this.route("categories.store"), this.form).then(() => {
        this.sending = false;
        this.form.title = null;
        this.form.category_parent_id = null;
        this.form.is_active = 1;
        this.form.addAgain = false;
      });
    },
  },
};
</script>
