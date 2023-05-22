<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('subjects')">Subjects</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Subject
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.name"
            :errors="$page.errors.name"
            class="pr-6 pb-8 w-full lg"
            label="Subject Name"
          />
          <textarea-input
            v-model="form.description"
            :errors="$page.errors.description"
            class="pr-6 pb-8 w-full lg"
            label="Description"
          />
          <text-input
            v-model="form.subject_code"
            :errors="$page.errors.subject_code"
            class="pr-6 pb-8 w-full lg"
            label="Subject Code (empty if not available)"
          />
          <text-input
            v-model="form.sks"
            :errors="$page.errors.sks"
            class="pr-6 pb-8 w-full lg"
            label="SKS Score (empty if not available)"
          />
           <text-input
            v-model="form.subject_type"
            :errors="$page.errors.subject_type"
            class="pr-6 pb-8 w-full lg"
            label="Subject Type (empty if not available) , Ex : Wajib Program Study"
          />
          <select-input
            v-model="form.is_active"
            :errors="$page.errors.is_active"
            class="pr-6 pb-8 w-full lg"
            label="Status"
          >
            <!-- <option :value="null" /> -->
            <option value="1" selected>Active</option>
            <option value="0">Not Active</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            v-on:click="addAnother"
            class="btn-bland mr-2 cursor-pointer"
          >Create Subject and Add More</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Create Subject</loading-button>
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
  metaInfo: { title: "Create Subject" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        name: null,
        subject_code: null,
        subject_type: null,
        sks: null,
        sks: null,
        description: null,
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
      this.$inertia.post(this.route("subjects.store"), this.form).then(() => {
        this.sending = false;
        this.form.name = null;
        this.form.description = null;
        this.form.subject_code = null;
        this.form.subject_type = null;
        this.form.sks = null;
        this.form.is_active = 1;
        this.form.addAgain = false;
      });
    },
  },
};
</script>
