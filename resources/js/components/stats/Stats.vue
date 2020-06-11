<template>
   <div>
       <div class="row">
           <div class="col-md-6 col-sm-12">
               <app-non-zero-class-stats :timezone="timezone"></app-non-zero-class-stats>
           </div>
           <div class="col-md-6 col-sm-12">
               <app-attendance-stats :timezone="timezone"></app-attendance-stats>
           </div>
       </div>
   </div>
</template>

<script>
import NonZeroClassStats from './NonZeroClassStats.vue';
import AttendanceStats from './AttendanceStats.vue';

export default {
    props: ['timezone'],
    components: {
        'app-non-zero-class-stats' : NonZeroClassStats,
        'app-attendance-stats': AttendanceStats
    }
}
</script>
