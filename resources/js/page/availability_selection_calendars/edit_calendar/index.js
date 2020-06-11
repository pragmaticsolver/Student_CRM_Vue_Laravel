import EditCalendar from '../../../components/availabilitySelectionCalendars/EditCalendar.vue';

window.addEventListener('DOMContentLoaded', function() {
    const vm = new Vue({
        el: '#vue-app',
        components: {
            'app-edit-calendar' : EditCalendar
        }
    });
});
