<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('interval.index')">Interval</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Interval
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.minmark"
            type="number"
            :errors="$page.errors.minmark"
            class="pr-6 pb-8 w-full lg:w-1/2"
            label="Min mark"
            step="any"
          />
          <text-input
            v-model="form.maxmark"
            type="number"
            :errors="$page.errors.maxmark"
            class="pr-6 pb-8 w-full lg:w-1/2"
            label="Max mark"
            step="any"
          />
          <text-input
            v-model="form.minavg"
            type="number"
            :errors="$page.errors.minavg"
            class="pr-6 pb-8 w-full lg:w-1/2"
            label="Min average"
            step="any"
          />
          <text-input
            v-model="form.maxavg"
            type="number"
            :errors="$page.errors.maxavg"
            class="pr-6 pb-8 w-full lg:w-1/2"
            label="Max average"
            step="any"
          />
          <text-input
            v-model="form.alphabet"
            :errors="$page.errors.alphabet"
            class="pr-6 pb-8 w-full lg\"
            label="Alphabet"
          />
          <select-input v-model="form.status" :errors="$page.errors.status" class="pr-6 pb-8 w-full" label="Status">
            <option :value="null" />
            <option value="Lulus">Pass</option>
            <option value="Tidak Lulus">Not Pass</option>
          </select-input>

            <h1 class="mb-1">Level</h1>
            <v-select class="pr-6 pb-8 w-full" label="title" :reduce="level => level.id" v-model="form.level_id" :options="level"></v-select>
          </div>
        
        
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <span
            v-on:click="addAnother"
            class="btn-bland mr-2 cursor-pointer"
          >Create Interval and Add More</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Create Interval</loading-button>
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
  metaInfo: { title: "Create Interval" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  remember: "form",
  props: {
    level: Array,
  },
  data() {
    return {
      sending: false,
      form: {
        minmark: null,
        maxmark: null,
        minavg: null,
        maxavg: null,
        alphabet: null,
        status: null,
        level_id: null,
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
      this.$inertia.post(this.route("interval.store"), this.form).then(() => {
        this.sending = false;
        this.form.addAgain = false;
      });
    },
  },
};
</script>
