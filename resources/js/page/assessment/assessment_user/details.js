import TimeSlotPicker from '../../../components/availabilitySelectionCalendars/TimeSlotPicker.vue';

window.addEventListener('DOMContentLoaded', function() {
    var vm1 = new Vue({
        el: '#vue-app',
        components: {
            'app-time-slot-picker' : TimeSlotPicker
        }
    });
});