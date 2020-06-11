import Responses from '../../components/availabilitySelectionCalendars/responses/Responses.vue';

window.addEventListener('DOMContentLoaded', function() {
    const vm = new Vue({
        el: '#vue-app',
        components: {
            'app-availbility-selection-responses' : Responses
        }
    });
});
