<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-indigo-400 hover:text-indigo-600"
        :href="route('daterapors.index')"
      >Report Date</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Report Date
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <div class="pr-6 pb-8 w-full lg:w-1/1">
            <h1 class="mb-1">Group</h1>
            <v-select v-model="form.group_id" label="fullname" :reduce="groups => groups.id" :options="groups"></v-select>
            <div v-if="$page.errors.group_id" class="form-error">{{ $page.errors.group_id[0] }}</div>
          </div>

          <text-input
            v-model="form.date_rapor"
            type="date"
            :errors="$page.errors.date_rapor"
            class="pr-6 pb-8 w-full lg"
            label="Reprot Date"
          />

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
          >Create and Add new Date Rapor</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Create Date Rapor</loading-button>
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
  metaInfo: { title: "Create Date Rapor" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  remember: "form",
  props: {  
    groups: Array,
  },
  data() {
    return {
      sending: false,
      form: {
        group_id: null,
        date_rapor: null,
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
      this.$inertia.post(this.route("daterapors.store"), this.form).then(() => {
        this.sending = false;
        this.form.group_id = null;
        this.form.date_rapor = null;
        this.form.is_active = 1;
        this.form.addAgain = false;
      });
    },
  },
};
</script>
