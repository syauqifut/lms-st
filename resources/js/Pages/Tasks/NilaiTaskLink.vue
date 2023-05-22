<template>
  <div>
    <inertia-link :href="route('tasks.index', task.course_module_id)">
      <h1 class="mb-8 font-bold text-3xl text-blue-800 hover:text-purple-800"> {{ task.name }}</h1>
    </inertia-link>


    <div v-if="taskFile" class="bg-white rounded shadow overflow-hidden w-full m-2">
      <div class="m-5">
        <h1>Description : {{ taskFile.description }}</h1>
        <h1>Collecting time : {{ taskFile.tanggal_kumpul ? taskFile.tanggal_kumpul : 'Havent collected yet'}}</h1>
        <h1>Status : {{ taskFile.status == 0 ? 'Not Seen' : 'Already Viewed' }}</h1>
        <h1>Mark : {{ taskFile.mark ? taskFile.mark : 'No value yet' }}</h1>
        <h1>due date : {{ task.due_date }}</h1>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
  metaInfo: { title: 'Task Link Mark' },
  layout: Layout,
  components: {

  },
  props: {
    task: Object,
    taskFile: Object,
  },
  data() {
    return {

    }
  },
  methods: {

  },
}
</script>
