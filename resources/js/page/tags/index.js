import TagList from '../../components/tags/TagList.vue';

window.addEventListener('DOMContentLoaded', function() {
    const vm = new Vue({
        el: '#vue-app',
        components: {
            'app-tag-list' : TagList
        }
    });
});
