import AvailabilitySelectionCalendarList from '../../components/availabilitySelectionCalendars/AvailabilitySelectionCalendarList.vue';

window.addEventListener('DOMContentLoaded', function() {
    const vm = new Vue({
        el: '#vue-app',
        components: {
            'app-availbility-selection-calendar-list' : AvailabilitySelectionCalendarList
        }
    });
});
