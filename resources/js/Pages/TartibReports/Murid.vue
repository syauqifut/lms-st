<template>
  <div>
    <div class="bg-gray-100 font-bold rounded shadow-xl py-5 px-5 w-full mb-10">
      <h1 class="mb-8 font-bold text-3xl">Student Report of Tata Tertib</h1>

      <div class="w-full flex flex-wrap">
        <div class="w-full sm:w-1/2 md:w-2/4 px-3 text-left">
          <div class="p-5 xl:px-8 md:py-5">
            <h3 class="text-2xl mb-3">{{ user.fullname }}</h3>
            <h5 class="text-xl mb-3">Total Points : {{ sumPoinPositif - sumPoinNegatif }}</h5>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <h1 class="mb-8 font-bold text-3xl">&nbsp;&nbsp;Tata Tertib Positif ({{ sumPoinPositif }})</h1>
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Date</th>
          <th class="px-6 pt-6 pb-4">Group</th>
          <th class="px-6 pt-6 pb-4">Reporter</th>
          <th class="px-6 pt-6 pb-4">Code</th>
          <th class="px-6 pt-6 pb-4">Name of Positive Behavior</th>
          <th class="px-6 pt-6 pb-4">Point</th>
        </tr>
        <tr
          v-for="positif in tartibPositif"
          :key="positif.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ positif.date }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ positif.group.classes }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ positif.pelapor.fullname }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ positif.tartib.kode_pelanggaran }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ positif.tartib.nama_pelanggaran }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ positif.poin }}
            </div>
          </td>
        </tr>
        <tr v-if="tartibPositif.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="6">No positive report data.</td>
        </tr>
      </table>
    </div><br><br>

    <div class="bg-white rounded shadow overflow-x-auto">
      <h1 class="mb-8 font-bold text-3xl">&nbsp;&nbsp;Tata Tertib Negatif ({{ sumPoinNegatif }})</h1>
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Date</th>
          <th class="px-6 pt-6 pb-4">Group</th>
          <th class="px-6 pt-6 pb-4">Reporter</th>
          <th class="px-6 pt-6 pb-4">Code</th>
          <th class="px-6 pt-6 pb-4">Name of Negative Behavior</th>
          <th class="px-6 pt-6 pb-4">Point</th>
        </tr>
        <tr
          v-for="negatif in tartibNegatif"
          :key="negatif.id"
          class="hover:bg-gray-100 focus-within:bg-gray-100"
        >
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ negatif.date }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ negatif.group.classes }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ negatif.pelapor.fullname }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ negatif.tartib.kode_pelanggaran }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ negatif.tartib.nama_pelanggaran }}
            </div>
          </td>
          <td class="border-t">
            <div class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ negatif.poin }}
            </div>
          </td>
        </tr>
        <tr v-if="tartibNegatif.length === 0">
          <td class="border-t px-6 py-4 text-center" colspan="6">No negative report data.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'

export default {
  metaInfo: { title: 'Student Report of Tata Tertib' },
  layout: Layout,
  components: {

  },
  props: {
    user: Object,
    sumPoinPositif: Number,
    sumPoinNegatif: Number,
    tartibPositif: Array,
    tartibNegatif: Array,
  },
  data() {
    return {
      sending: false,
      form: {

      },
    }
  },

  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
