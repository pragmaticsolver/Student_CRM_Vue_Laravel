import Stats from '../../js/components/stats/Stats.vue';

window.addEventListener('DOMContentLoaded', function() {
    const vm = new Vue({
        el: '#vue-app',
        components: {
            'app-stats' : Stats
        },
        data : {
            timezone: app_timezone
        }
    });
});
