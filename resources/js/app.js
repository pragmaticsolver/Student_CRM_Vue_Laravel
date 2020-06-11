
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

var js_version = 2; // Basically increment this by one whenever app local storage needs to be cleared.

if(typeof localStorage.js_version === 'undefined' || localStorage.js_version === null) {
    localStorage.clear();
    localStorage.setItem('js_version', js_version);
}
if(localStorage.js_version != js_version){
    localStorage.clear();
}

import $ from 'jquery';
window.$ = window.jQuery = $;
import 'jquery-ui/ui/widgets/draggable.js';

import route from 'ziggy';
import { Ziggy } from 'ziggyRoutes'
window.route = route;
window.Ziggy = Ziggy;

// import 'dropzone/dist/dropzone.js';
window.moment = require('moment');

import Swal from 'sweetalert2/dist/sweetalert2.js';

window.trans = window.__ = function (string) {
  return _.get(window.i18n, string);
};
window.Swal = Swal;

$.fn.datetimepicker = require('eonasdan-bootstrap-datetimepicker');

// Vuejs related things can be moved to seprate files
import { BootstrapVue } from 'bootstrap-vue';
import StudentTags from './components/tags/StudentTags.vue';

window.Vue = require('vue');
Vue.mixin({
    methods: {
        route: (name, params, absolute) => window._route(name, params, absolute, Ziggy),
    }
});
Vue.use(BootstrapVue);
Vue.component('app-student-tags', StudentTags);

// Make trans function available to vue.js
Vue.prototype.trans = window.trans;
Vue.prototype.__ = window.__;
Vue.prototype.$swal = window.Swal;
Vue.prototype.copyToClipboard = function(text, event){
    window.copyToClipboard(text, event.target)
};
Vue.prototype.$eventBus = new Vue({
    data: {
        fetched: false,
        allTags : [],
        display_expanded_tags: false
    },
    methods: {
        fetchAllTags: function(){
            if(this.fetched == false)
            {
                this.fetched = true;
                axios.get(route('tags.records').url())
                .then(res => {
                    let data = res.data;
                    this.allTags = data;
                    this.$emit('allTags', this.allTags);
                });
            }
            else
            {
                this.$emit('allTags', this.allTags);
            }
        }
    }
}); // Global event bus

Vue.prototype.showMessage = function(variant, message){
    this.$bvToast.toast(message, {
        // title: variant.charAt(0).toUpperCase() + variant.slice(1),
        variant: variant,
        autoHideDelay: 5000,
        solid: true,
        appendToast: true
    });
};

Vue.prototype.showError = function(error_message){
    this.$swal.fire({
        title: 'Error',
        text: error_message,
        icon: 'error',
    });
};

require('./main');

// Fix select2 not appearing on bootsratp modal
//fix modal force focus
$.fn.modal.Constructor.prototype._enforceFocus = function() {};
