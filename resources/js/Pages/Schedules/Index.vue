<template>
  <div class="">
    <h1 class="text-5xl mb-5">Schedule of Activities</h1>

    <FullCalendar
      :options="calendarOptions"
    />
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

export default {
  metaInfo: { title: 'Schedules' },
  layout: Layout,
  components: {
    FullCalendar,
  },
  props: {
    courseModules: Array,
  },
  data() {
    return {
      calendarOptions: {
        plugins: [interactionPlugin , dayGridPlugin],
        initialView: 'dayGridMonth',
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        height: 800,
        locale: 'en',
        displayEventEnd: true,
        dayMaxEvents: true,
        // displayEventTime: true,
        events: this.courseModules,
      },
      data:null,
    }
  },
  methods: {

    handleEventClick: function(info) {
      this.$inertia.replace(info.event._def.extendedProps.route)
    },
    // handleDateClick: function(arg) {
    //   // alert('date click! ' + arg.dateStr)
    //   this.$inertia.replace(this.route('schedules.day', arg.dateStr))
    // },
  },
}
</script>

<style scoped>

</style>