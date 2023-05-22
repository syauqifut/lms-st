<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('persentase')">Percentage</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Percentage
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Category</h1>
            <v-select v-model="form.category_id" label="title" :reduce="kategori => kategori.id" :options="kategori" :errors="$page.errors.category_id"></v-select>
          </div>
          <select-input v-model="form.task_type" :errors="$page.errors.task_type" class="pr-6 pb-8 w-full" label="Task Type">
            <option :value="null" />
            <option value="Tugas">Tugas</option>
            <option value="UTS">UTS</option>
            <option value="UAS">UAS</option>
            <option value="Perform">Perform</option>
            <option value="Sakit">Sakit</option>
            <option value="Izin">Izin</option>
            <option value="Alpha">Alpha</option>
          </select-input>
          <text-input
            v-model="form.persen"
            type="number"
            :errors="$page.errors.persen"
            class="pr-6 pb-8 w-full lg:w-full"
            label="Percentage"
            step="any"
          />
        </div>
        
        
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            v-on:click="addAnother"
            class="btn-bland mr-2 cursor-pointer"
          >Create Percentage and Add More</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Create Percentage</loading-button>
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
  metaInfo: { title: "Create Percentage" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  remember: "form",
  props: {
    kategori: Array,
  },
  data() {
    return {
      sending: false,
      form: {
        persen: null,
        task_type: null,
        category_id: null,
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
      this.$inertia.post(this.route('persentase.store'), this.form).then(() => {
        this.sending = false;
        this.form.addAgain = false;
      });
    },
  },
};
</script>
