<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tartibs')">Tartibs</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create Tata Tertib
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input
            v-model="form.kode_pelanggaran"
            :errors="$page.errors.kode_pelanggaran"
            class="pr-6 pb-8 w-full lg"
            label="Code Violation"
          />
          <text-input
            v-model="form.nama_pelanggaran"
            :errors="$page.errors.nama_pelanggaran"
            class="pr-6 pb-8 w-full lg"
            label="Name Violation"
          />
          <select-input
            v-model="form.kategori"
            :errors="$page.errors.kategori"
            class="pr-6 pb-8 w-full lg"
            label="Violation Category"
          >
            <!-- <option :value="null" /> -->
            <option value="POSITIF" selected>Positif</option>
            <option value="RINGAN" >Ringan</option>
            <option value="SEDANG" >Sedang</option>
            <option value="BERAT" >Berat</option>
            <option value="SANGAT_BERAT" >Sangat Berat</option>
          </select-input>

           <select-input
            v-model="form.jenis"
            :errors="$page.errors.jenis"
            class="pr-6 pb-8 w-full lg"
            label="Violation Type"
          >
            <!-- <option :value="null" /> -->
            <option value="POSITIF" >POSITIF</option>
            <option value="NEGATIF" selected>NEGATIF</option>
          </select-input>

          <text-input
            v-model="form.skor"
            :errors="$page.errors.skor"
            class="pr-6 pb-8 w-full lg"
            label="Score"
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
          >Create and Add new Tartib</span>
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Create Tartib</loading-button>
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
  metaInfo: { title: "Create Tartibs" },
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
        nama_pelanggaran: null,
        kode_pelanggaran: null,
        jenis: null,
        kategori: null,
        skor: null,
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
      this.$inertia.post(this.route("tartibs.store"), this.form).then(() => {
        this.sending = false;
        this.form.name = null;
        this.form.description = null;
        this.form.tartib_code = null;
        this.form.tartib_type = null;
        this.form.sks = null;
        this.form.is_active = 1;
        this.form.addAgain = false;
      });
    },
  },
};
</script>
