<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <!-- <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('levels.index')">Level</inertia-link> -->
      <!-- <span class="text-indigo-400 font-medium">/</span>  -->
      {{course.title}}
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <input type="hidden" v-model="form.addAgain" />
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap text-3xl flex justify-center">
          <vue-stars name="demo" :max="5" v-model="form.star" :readonly="false" />
        </div>
        <textarea-input
                v-model="form.review"
                :errors="$page.errors.description"
                class="pr-6 pb-8 w-full lg ml-3"
                label="Deskripsi"
              />
        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
          <loading-button
            ref="btnsubmit"
            :loading="sending"
            class="btn-indigo"
            type="submit"
          >Submit</loading-button>
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
import { VueStars } from "vue-stars"

export default {
  metaInfo: { title: "Buat Reviews" },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
    VueStars
  },
  props: {
    course: Object,
    review: Object,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        star:this.review.star,
        review:this.review.review,
      },
    };
  },
  methods: {
    submit() {
      this.sending = true;
      this.$inertia.put(this.route("reviews.update", this.review.id), this.form).then(() => {
        this.sending = false;
      });
    },
  },
};
</script>
