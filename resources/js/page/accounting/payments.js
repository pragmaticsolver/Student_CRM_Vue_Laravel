import PaymentList from '../../components/accounting/PaymentList.vue';

window.addEventListener('DOMContentLoaded', function() {
    const vm = new Vue({
        el: '#vue-app',
        components: {
            'app-payment-list' : PaymentList
        }
    });
});
