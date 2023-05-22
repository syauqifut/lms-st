<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      Rekap Absensi Guru
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit" target="_blank">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.date_start"
            type="date"
            class="pr-6 pb-8 w-full lg"
            label="Date Start"
          />
        </div>
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.date_end"
            type="date"
            class="pr-6 pb-8 w-full lg"
            label="Date End"
          />
        </div>
        <div class="px-8 py-4 flex justify-end items-center">
          <loading-button
            :loading="sending"
            class="btn-indigo"
            type="submit"
            target="_blank"
            formtarget="_blank"
          >Download</loading-button>
          <!-- <a href="#" v-on:click="submit" class="btn-indigo" target="_blank">Download</a> -->
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
  metaInfo: { title: "Rekap Absensi Guru" },
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
        date_start: null,
        date_end: null,
      },
    };
  },
  methods: {
    submit() {
      this.sending = true;
      // window.open(href, '_blank');
      this.$inertia.post(this.route("absensi-downloadrekapguru"), this.form).then(() => {
        this.sending = false;
        this.form.date_start = null;
        this.form.date_end = null;
      });
    },
  },
};
</script>
